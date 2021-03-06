<?php

namespace App\Http\Controllers\Conference;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use DB;
use Auth;
use Entrust;
use JWTAuth;
use App\Conference;
use App\Utility\RoleCreate;
use App\Utility\PermissionNames;
use App\Utility\RoleNames;
use App\Utility\ConferenceRegistrationUtils;
use App\Event;
use App\Models\Permission;
use Config;
use Log;

use App\UserConference;
use App\Jobs\SendUpdateEmail;

class MainController extends Controller
{

    public function __construct() {
        //Allow info requests without a token.  May need to do extra
        //auth stuff if they want detailed info, but right now we don't
        //make that distinction
        $this->middleware('jwt.auth.rejection', ['except' => ['getInfo', 'getInfoList']]);
        $this->middleware('jwt.check', ['only' => ['getInfo', 'getInfoList']]);
        $this->middleware('permission:' . PermissionNames::CreateConference(), ['only' => ['createNew']]);
    }

    /**
     * Validates json for basic conference details.
     */
    private function validateConferenceJson($req) {
        $this->validate($req, [
            "name" => "required",
            "start" => "required|date_format:Y-m-d",
            "end" => "required|date_format:Y-m-d",
            "location" => "required",
            "description" => "string",
            "hasTransportation" => "boolean|required",
            "hasAccommodations" => "boolean|required"]);
    }

    private function validateDataRequest($req) {
        $this->validate(
            $req,
            [
                "includePermissions" => "boolean",
                "includeRegistration" => "boolean"
            ]);
    }

    /**
     * Converts a conference object from the Eloquent object
     * into a json array.
     */
    private function conferenceResponseJSONArray($conference, $req) {
        $data = $conference->toArray();
        if ($req->input("includePermissions")) {
            $data["permissions"] = $this->buildPermissionList($conference->id);
        }

        if ($req->input("includeRegistration")) {
            $data["registered"] = ConferenceRegistrationUtils::getAccountRegistrationData($conference->id);
        }

        $data["end"] = $data["dateEnd"];
        unset($data["dateEnd"]);
        $data["start"] = $data["dateStart"];
        unset($data["dateStart"]);

        $data["name"] = $data["conferenceName"];
        unset($data["conferenceName"]);

        return $data;
    }

    /**
     * Builds a Conference object from a request.  This assumes the
     * request is valid, so use 'validateConferenceJson' first
     */
    private function assignInputToConference($req, $conf) {
        $conf->conferenceName = $req->input('name');
        $conf->dateStart = $req->input('start');
        $conf->dateEnd = $req->input('end');
        $conf->location = $req->input('location');
        $conf->description = $req->input('description');
        $conf->hasTransportation = $req->input('hasTransportation');
        $conf->hasAccommodations = $req->input('hasAccommodations');
    }

    /**
     * Creates a new conference, given valid json.
     */
    public function createNew(Request $req) {
        $this->validateConferenceJson($req);

        Log::info("Creating new conference " . $req->input('name'));

        return DB::transaction(function () use ($req) {
            $conf = new Conference;
            $this->assignInputToConference($req, $conf);
            $conf->save();

            $role = RoleCreate::AllConferenceRoles($conf->id);
            $user = Auth::user();
            $user->attachRole($role);

            return response()->json(['id' => (int)$conf->id]);
        });
    }

    /**
     * Gets info about a specific conference.
     */
    public function getInfo(Request $req, $id) {
        $this->validateDataRequest($req);
        $conference = Conference::find($id);

        if (is_null($conference)) {
            Log::debug("Got a request for non-existant conference ID " . $id);
            return response("No conference for id {$id}.", 404);
        }

        return response()->json($this->conferenceResponseJSONArray($conference, $req));
    }

    /**
     * Gets a json array with all conferences.
     */
    public function getInfoList(Request $req) {
        $this->validateDataRequest($req);
        $conferences = Conference::all();

        $jsonArrays = [];
        foreach ($conferences as $conf) {
            array_push($jsonArrays, $this->conferenceResponseJSONArray($conf, $req));
        }
        return response()->json($jsonArrays);
    }

    /**
     * Replaces a given conference with the new values, given valid json.
     */
    public function replace(Request $req, $id) {
        if (!Entrust::can(PermissionNames::ConferenceInfoEdit($id))) {
            return response("", 403);
        }
        $this->validateConferenceJson($req);
        $conf = Conference::find($id);

        if (is_null($conf)) {
            return response("Conference {$id} does not exist.", 400);
        }
        $this->assignInputToConference($req, $conf);
        $conf->save();
        Log::info("Conference info for " . $conf->conferenceName . " edited.");

        $recipientModels = UserConference::where('conferenceID', $id)->with('user.account')->get();
        $recipients=[];

        foreach($recipientModels as $model) {
            if($model->user->account->receiveUpdates) {
                $recipients[] = $model->user->account->email;
            } else {
                Log::debug("Discarding {$model->user->account->email} as updates aren't enabled");
            }
        }

        Log::info("Dispatching conference update for " . sizeof($recipients) . " recipients");

        $this->dispatch(new SendUpdateEmail("Conference Updated", "update-notification",
                          ['typestr' => 'conference', 'name' => $conf->conferenceName, 'link' => config('app.url') . '/dashboard/conferences/list'],
                          $recipients));
        return '';
    }

    /**
     * Deletes a conference.
     */
    public function delete($id) {
        if (!Entrust::can(PermissionNames::ConferenceInfoEdit($id))) {
            return response("", 403);
        }
        DB::transaction(function () use ($id) {
            $events = Event::where('conferenceID', $id)->get();

            $pnames =
                array_merge(PermissionNames::AllConferencePermissions($id),
                            PermissionNames::ExclusiveConferencePermissions($id));
            $evtIds = [];
            foreach ($events as $e) {
                $pnames = array_merge($pnames, PermissionNames::AllEventPermissions($e->id));
                echo $e;
                $evtIds[] = $e->id;
            }

            Permission::whereIn('name', $pnames)->delete();

            RoleCreate::deleteConferenceRoles($id);

            RoleCreate::deleteEventRoles($evtIds);

            Conference::destroy($id);
        });

        Log::info("Conference with ID {$id} deleted");

        return '';
    }

    public function getPermissions($confId) {
        $permissions = $this->buildPermissionList($confId);
        return response()->json($permissions);
    }

    private function buildPermissionList($confId) {
         $permissions = [];

         foreach (PermissionNames::AllConferencePermissions($confId) as $pname) {
            $this->checkAddPermission(
                $pname,
                $permissions);
         }

         foreach(Event::where('conferenceID', $confId)->get() as $e) {
             if (Entrust::can(PermissionNames::EventDetailView($e->id))) {
                $permissions[] = 'conference-view-event-reports';
                break;
             }
         }

         return $permissions;
    }

    private function checkAddPermission($pname, &$permList) {
        if (Entrust::can($pname)) {
            $permList[] = PermissionNames::normalizePermissionName($pname);
        }
    }
}
