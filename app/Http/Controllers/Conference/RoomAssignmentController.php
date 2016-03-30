<?php

namespace App\Http\Controllers\Conference;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Entrust;
use Auth;

use App\UserConference;
use App\UserRoom;
use App\RoomSet;
use App\Residence;
use App\Utility\PermissionNames;

class RoomAssignmentController extends Controller
{
    public function __construct() {
        $this->middleware('jwt.auth.rejection');
    }

    private function findRoomUsers($resId, $roomName) {
        return UserRoom::where('roomName', $roomName)
            ->whereHas("roomSet", function ($query) use ($resId) {
                $query->where("residenceID", $resId);
            })->with('registration.user')->get();
    }

    private function validateAssignmentRequest($req) {
        $this->validate($req, [
            'registrationIds' => 'idarray|required',
            'roomName' => 'string|required',
            'roomSet' => 'numeric|required'
        ]);
    }

    public function assignRoom(Request $req, $confId) {
        if(!Entrust::can(PermissionNames::ConferenceRoomEdit($confId))) {
            return response("",  403);
        }

        $this->validateAssignmentRequest($req);
        $roomName = $req->input('roomName');
        $registrationIDs = $req->input('registrationIds');

        if(UserConference::whereIn('id', $registrationIDs)->count() != sizeof($registrationIDs)) {
            return response()->json(['message'=>'nonexistant_registration'], 400);
        }

        if(UserRoom::whereIn('registrationID', $registrationIDs)->count() > 0) {
            return response()->json(['message'=>'already_assigned'], 400);
        }

        $roomSet = RoomSet::find($req->input('roomSet'));

        $currentUsers = $this->findRoomUsers($roomSet->residenceID, $roomName);

        foreach ($currentUsers as $userRoom) {
            if ($userRoom->roomSetID != $roomSet->id) {
                return response()->json(["message"=>'name_not_in_set'], 400);
            }
        }

        $capacity = $roomSet->type->capacity - count($currentUsers);
        $registrations = $req->input('registrations');

        if ($capacity < sizeof($registrations)) {
            return response()->json(['message'=>'insufficient_capacity'], 400);
        }

        $roomAssignments = [];

        foreach ($registrationIDs as $registration) {
            $roomAssignments[] =
                ['roomSetID' => $roomSet->id,
                 'registrationID' => $registration,
                 'roomName' => $roomName];
        }

        UserRoom::insert($roomAssignments);

        return UserRoom::whereIn('registrationID', $registrationIDs)->where('roomSetID', $roomSet->id)->select('id', 'registrationID')->get();
    }

    public function getRoomUsers($confId, $resId, $roomName) {
        if(!Entrust::can(PermissionNames::ConferenceRoomEdit($confId))) {
            return response("", 403);
        }

        $res = Residence::find($resId);
        if (is_null($res) || $res->conferenceID != $confId) {
            return response("", 404);
        }

        return $this->findRoomUsers($resId,  $roomName);
    }

    public function listAssignments(Request $req, $confId) {
        if($req->input('all') && !Entrust::can(PermissionNames::ConferenceRoomEdit($confId))) {
            return response("", 403);
        }

        if($req->input('all')){
            return UserRoom::whereHas('registration', function($query) use ($confId) {
                $query->where('conferenceID', $confId);
            })->with('registration.user')->get();
        } else {
            return UserRoom::whereHas('registration', function($query) use ($confId) {
                $query->where('conferenceID', $confId);
                $query->whereHas('user', function ($userQuery) {
                    $userQuery->where("accountID", Auth::user()->id);
                });
            })->with('registration.user')->get();
        }
    }

    public function deleteAssignment($confId, $assignmentId) {
        if(!Entrust::can(PermissionNames::ConferenceRoomEdit($confId))) {
            return response("", 403);
        }

        $assignment = UserRoom::find($assignmentId);
        if($assignment->registration->conferenceID != $confId) {
            return response("", 404);
        }

        $assignment->delete();
    }

    public function missingAssignments($confId) {
        if(!Entrust::can(PermissionNames::ConferenceRoomEdit($confId))) {
            return response("", 403);
        }

        return UserConference::where("needsAccommodation", true)
                ->has("room", "<", 1)
                ->where('conferenceID', $confId)
                ->with("user")
                ->get();
    }

    public function roomsInSet($confId, $setId) {
        if(!Entrust::can(PermissionNames::ConferenceRoomEdit($confId))) {
            return response("", 403);
        }

        $set = RoomSet::with("residence")->find($setId);
        if (is_null($set) || $set->residence->conferenceID != $confId) {
            return response()->json(["message" => "no_such_set"], 404);
        }

        return UserRoom::selectRaw('roomName, count(*) as currentUsers')
            ->where('roomSetID', $set->id)
            ->groupBy('roomName')->get();
    }
}
