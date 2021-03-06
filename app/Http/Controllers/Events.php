<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Auth;
use Entrust;
use JWTAuth;
use App\Event;
use App\User;
use App\UserEvent;
use App\Conference;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Utility\RoleCreate;
use App\Utility\PermissionNames;
use App\Utility\RoleNames;
use App\Utility\CheckDependents;
use App\Utility\ConferenceRegistrationUtils;
use Log;
use App\Jobs\SendUpdateEmail;

use App\Models\Permission;

class Events extends Controller {

    public function __construct() {
        //Allow info requests without a token.  May need to do extra
        //auth stuff if they want detailed info, but right now we don't
        //make that distinction
        $this->middleware('jwt.auth.rejection');
    }

    private function rewriteEventWithAttendees(&$e) {
        $e['remainingCapacity'] = $e['capacity'] - sizeof($e['attendees']);
        unset($e['attendees']);
    }

    /**
     * Display a listing of all events in the database.
     * @return Response
     */
    public function index(Request $req, $id = null) {
        if ($id == null) {
            $events = Event::with("attendees")->orderBy('id', 'asc')->get()->toArray();
            $events = array_map(function ($e) use ($req) { return $this->show($req, $e); }, $events);
            return $events;
        } else {
            $evt = Event::with("attendees")->find($id);
            if (isset($evt)) {
                return $this->show($req, $evt);
            } else {
                return response()->json(["message" => "event_not_found"], 404);
            }
        }
    }

    /**
     * Display a listing of events given the conferenceID.
     * @return Response
     */
    public function getEventByConferenceID(Request $req, $conferenceID) {
        $conf = Conference::find($conferenceID);
        if (is_null($conf)) {
            return response("No conference for id {$conferenceID}.", 405);
        }

        //Select events, populate with "attendees" so we can calculate the
        //remaining capacity
        $event = Event::with("attendees")
                    ->where('conferenceID', $conferenceID)->get();

        $evtArray = $event->toArray();

        if (sizeof($evtArray) == 0) {
            return response("No events for conferenceID {$conferenceID}.", 404);
        }

        $evtArray = array_map(function ($e) use ($req) { return $this->show($req, $e); }, $evtArray);
        return $evtArray;
    }

    private function validateEventInput($req) {
        $this->validate($req,[
            "eventName" => 'required|string',
            "date" => 'required|date_format:Y-m-d',
            "location"=> 'string|required',
            "startTime" => 'required|date_format:H:i:s',
            "endTime" => 'required|date_formate:H:i:s',
            "capacity" => 'required|numeric',
            "description" => 'required|string'
        ]);
    }

    /**
     * Store a newly created resource in storage.
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request, $id) {
        $conf = Conference::find($id);
        if (is_null($conf)) {
            return response("No conference for id {$id}.", 405);
        }

        if (!Entrust::can(PermissionNames::ConferenceEventCreate($id))) {
            return response("Permission not found", 403);
        }

        return DB::transaction(function () use ($request, $id) {
          $event = new Event;
          $event->eventName = $request->input('eventName');
          $event->date = $request->input('date');
          $event->location = $request->input('location');
          $event->startTime = $request->input('startTime');
          $event->endTime = $request->input('endTime');
          $event->capacity = $request->input('capacity');
          $event->description = $request->input('description');
          $event->conferenceID = $id;
          $event->save();

          $role = RoleCreate::AllEventRoles($event->id);
          $user = Auth::user();
          $user->attachRole($role);

          return response()->json(['id' => $event->id]);
      });
    }

    /**
     * Display the event given the eventID.
     * @param  int  $id
     * @return Response
     */
    public function show(Request $req, $event) {
        $this->rewriteEventWithAttendees($event);

        if($req->input("includeRegistration")) {
            $event["registrations"] = ConferenceRegistrationUtils::getAccountEventRegistrationData($event['id']);
        }

        if($req->input("includePermissions")) {
            $event["permissions"] = $this->buildPermissionList($event['id']);
        }

        return $event;
    }

    /**
     * Update the specified event give the eventID.
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, $id) {
        if (!Entrust::can(PermissionNames::EventInfoEdit($id))) {
            return response("Permission not found", 403);
        }

        $event = Event::find($id);
        if (is_null($event)) {
            return response("No event for id {$id}.", 404);
        }

        $event->eventName = $request->input('eventName');
        $event->date = $request->input('date');
        $event->location = $request->input('location');
        $event->startTime = $request->input('startTime');
        $event->endTime = $request->input('endTime');
        $event->capacity = $request->input('capacity');
        $event->description = $request->input('description');
        $event->conferenceID = $request->input('conferenceID');
        $event->save();

        $recipientModels = UserEvent::where('eventID', $id)->with('user.account')->get();
        $recipients=[];

        foreach($recipientModels as $model) {
            if($model->user->account->receiveUpdates) {
                $recipients[] = $model->user->account->email;
            }
        }

        Log::info("Dispatching event update for " . sizeof($recipients) . " recipients");

        $this->dispatch(new SendUpdateEmail("Event Updated", "update-notification",
                          ['typestr' => 'event', 'name' => $event->eventName . " for conference " . $event->conference->conferenceName,
                           'link' => config('app.url') . '/dashboard/' . $event->conferenceID . '/events'],
                          $recipients));

        return response()->json(['id' => $event->id]);
    }

    /**
     * Remove the event given the eventID.
     * @param  int  $id
     * @return Response
     */
    public function destroy($id) {
        if (!Entrust::hasRole(RoleNames::EventManager($id))) {
            return response("Permission not found", 403);
        }

        return DB::transaction(function () use ($id) {
            $event = Event::find($id);
            if (is_null($event)) {
                return response("No event for id {$id}.", 404);
            }

            $event->delete();
            Permission::whereIn('name', PermissionNames::AllEventPermissions($id))->delete();
            RoleCreate::deleteEventRoles($id);
            return response()->json(['id' => $event->id]);
        });
    }

    private function validateInputIDs($req) {
        $this->validate($req, [
            "ids" => "idarray|required"
        ]);
    }

    private function checkConferenceAttendees($eventId, $userIds) {
        //Select event with conference.attendees populated, but only
        //populate with attendees of the conference within our ID list
        $withAttendees = Event::where("id", $eventId)
                ->with([
                    "conference.attendees" => function($query) use ($userIds) {
                        $query->whereIn("userID", $userIds);
                    }])->get()->first();
        return sizeof($withAttendees->conference->attendees);
    }

    /**
     * Register the user given the userID to the event given the eventID.
     * @param  int  $id, int $eventId
     * @return Response
     */
    public function register(Request $req, $id) {
        $event = Event::find($id);
        if (is_null($event)) {
            return response()->json(["message"=>"no_such_event"], 404);
        }

        $this->validateInputIDs($req);

        $idList = $req->input('ids');
        if (!CheckDependents::dependentsOkay($idList)) {
            return response()->json(["message"=>"bad_dependents"], 400);
        }

        if(!$this->checkConferenceAttendees($id, $idList)) {
            return response()->json(["message"=>"not_in_conference"], 400);
        }

        $eventUser = UserEvent::where('eventID', $id)
                        ->whereIn('userID',$idList)->get();
        if (count($eventUser) > 0) {
            return response()->json(["message"=>"already_registered"], 400);
        }

        $data = array_map(
            function ($userId) use ($id){
                return ["userID" => $userId, "eventID" => $id];
            },
            $idList);
        UserEvent::insert($data);
        return UserEvent::select(["id", "userID"])
                    ->whereIn("userID", $idList)->get();
    }

    public function deleteRegistration(Request $req, $eventId,$id) {
        $event = Event::find($id);
        if (is_null($event)) {
            return response()->json(["message"=>"no_such_event"], 404);
        }
        $registration = UserEvent::with('user')->find($id);
        if (is_null($registration)) {
            return response()->json(["message"=>"registration_deleted"]);
        }
        if (!Entrust::can(PermissionNames::ConferenceRegistrationApproval($event->conferenceID))
                && $registration->user->accountID != Auth::user()->id) {
            return response()->json(["message"=>"cannot_edit_registration"], 403);
        }

        $registration->delete();
        return response()->json(["message"=>"registration_deleted"]);
    }

    private function buildPermissionList($eventId) {
        $permissions = [];
        foreach (PermissionNames::AllEventPermissions($eventId) as $pname) {
            Log::debug("Checking permission {$pname} for " . Auth::user()->email);
            if (Entrust::can($pname)) {
                Log::debug("Permission enabled");
                $permissions[] = PermissionNames::normalizePermissionName($pname);
            }
        }
        return $permissions;
    }

}
