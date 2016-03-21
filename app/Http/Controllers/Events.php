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
use Config;

class Events extends Controller {

    public function __construct() {
        //Allow info requests without a token.  May need to do extra
        //auth stuff if they want detailed info, but right now we don't
        //make that distinction
        $this->middleware('jwt.auth');
    }

    /**
     * Display a listing of all events in the database.
     * @return Response
     */
    public function index($id = null) {
        if ($id == null) {
            return Event::orderBy('id', 'asc')->get();
        } else {
            return $this->show($id);
        }
    }

    /**
     * Display a listing of events given the conferenceID.
     * @return Response
     */
    public function getEventByConferenceID($conferenceID) {
        $conf = Conference::find($conferenceID);
        if (is_null($conf)) {
            return response("No conference for id {$conferenceID}.", 405);
        }

        $event = Event::where('conferenceID', $conferenceID)->get();
        if (count($event) == 0) {
            return response("No events for conferenceID {$conferenceID}.", 404);
        }
        return $event;
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

        return DB::transaction(function () use ($request) {
          $event = new Event;
          $event->eventName = $request->input('eventName');
          $event->date = $request->input('date');
          $event->location = $request->input('location');
          $event->startTime = $request->input('startTime');
          $event->endTime = $request->input('endTime');
          $event->capacity = $request->input('capacity');
          $event->description = $request->input('description');
          $event->conferenceID = $request->input('conferenceID');
          $event->save();

          $role = RoleCreate::EventManager($event->id);
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
    public function show($id) {
        $event = Event::find($id);
        if (count($event) == 0) {
            return response("No event for id {$id}.", 404);
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
            return response("", 403);
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

        return response()->json(['id' => $event->id]);
    }

    /**
     * Remove the event given the eventID.
     * @param  int  $id
     * @return Response
     */
    public function destroy($id) {
        if (!Entrust::hasRole(RoleNames::EventManager($id))) {
            return response("", 403);
        }

        $event = Event::find($id);
        if (is_null($event)) {
            return response("No event for id {$id}.", 404);
        }

        $event->delete();
        return response()->json(['id' => $event->id]);
    }

    /**
     * Register the user given the userID to the event given the eventID.
     * @param  int  $id, int $eventId
     * @return Response
     */
    public function register($id, $userId) {
        $event = Event::find($id);
        if (is_null($event)) {
            return response("No event for id {$id}.", 404);
        }

        $user = User::find($userId);
        if (is_null($user)) {
            return response("No user for id {$userId}.", 404);
        }

        $eventUser = UserEvent::where('eventID','=',$id)->where('userID','=',$userId)->get();
        if (count($eventUser) > 0) {
            return response("userId {$userId} already registered for event id {$id}.", 404);
        }

        $userEvent = new UserEvent;
        $userEvent->userID = $userId;
        $userEvent->eventID = $id;
        $userEvent->save();

        return response()->json(['User Resistered' => $userEvent->id]);
    }

}
