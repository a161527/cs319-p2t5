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
        $this->middleware('jwt.auth');
    }

    private function validateAssignmentRequest($req) {
        $this->validate($req, [
            'registrations.*.id' => 'numeric|required',
            'registrations.*.roomName' => 'string|required',
            'roomSet' => 'numeric|required'
        ]);
    }

    public function assignRoom(Request $req, $confId) {
        if(!Entrust::can(PermissionNames::ConferenceRoomEdit($confId))) {
            return response("",  403);
        }

        $this->validateAssignmentRequest($req);

        $roomSet = RoomSet::find($req->input('roomSet'));
        $capacity = $roomSet->type->capacity - count($roomSet->assignments);
        $registrations = $req->input('registrations');

        if ($capacity < sizeof($registrations)) {
            return response()->json(['message'=>'insufficient_capacity'], 400);
        }

        $registrationIDs = array_map(function($r) {return $r['id'];}, $registrations);

        if(UserConference::whereIn('id', $registrationIDs)->count() != sizeof($registrations)) {
            return response()->json(['message'=>'nonexistant_registration'], 400);
        }

        if(UserRoom::whereIn('registrationID', $registrationIDs)->count() > 0) {
            return response()->json(['message'=>'already_assigned'], 400);
        }

        $roomAssignments = [];

        foreach ($registrations as $registration) {
            $roomAssignments[] =
                ['roomSetID' => $roomSet->id,
                 'registrationID' => $registration['id'],
                 'roomName' => $registration['roomName']];
        }

        UserRoom::insert($roomAssignments);

        return UserRoom::whereIn('registrationID', $registrationIDs)->select('id')->get();
    }

    public function getRoomUsers($confId, $resId, $roomId) {
        if(!Entrust::can(PermissionNames::ConferenceRoomEdit($confId))) {
            return response("", 403);
        }

        $res = Residence::find($resId);
        if (is_null($res) || $res->conferenceID != $confId) {
            return response("", 404);
        }

        $room = RoomSet::find($roomId);
        if (is_null($room) || $room->residenceID != $resId) {
            return response("", 404);
        }

        return UserRoom::where('roomSetID', $room->id)->with('registration.user')->get();
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
                $query->where('userID', Auth::user()->id);
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
                ->with("user")
                ->get();
    }
}
