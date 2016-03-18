<?php

namespace App\Http\Controllers\Conference;

use Illuminate\Http\Request;

use Entrust;
use DB;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Residence;
use App\RoomSet;
use App\RoomType;

use App\Utility\PermissionNames;

class RoomSetupController extends Controller
{
    public function __construct() {
        $this->middleware('jwt.auth');
    }

    public function getResidenceList($confId) {
        if (!Entrust::can(PermissionNames::ConferenceRoomEdit($confId))) {
            return response("", 403);
        }

        return Residence::where("conferenceID", $confId)->get();
    }

    public function uploadRoomData($confId) {

    }

    public function createResidence(Request $req, $confId) {
        if (!Entrust::can(PermissionNames::ConferenceRoomEdit($confId))) {
            return response("", 403);
        }
        $this->validateResidence($req);
        $residence = new Residence;
        $residence->name = $req->input('name');
        $residence->location = $req->input('location');
        $residence->conferenceID = $confId;
        $residence->save();

        return response()->json(["id" => $residence->id]);
    }

    public function getResidenceRooms($confId, $residenceId) {
        if (!Entrust::can(PermissionNames::ConferenceRoomEdit($confId))) {
            return response("", 403);
        }

        $res = Residence::find($residenceId);
        if (is_null($res) || $res->conferenceID != $confId) {
            return response("", 404);
        }
        $res->load('roomSets.type');

        $result = [];
        foreach ($res->roomSets()->get() as $roomSet) {
            $setRepr = ["type" => $roomSet->type->toArray()];
            if (is_null($roomSet->name)) {
                $setRepr["rangeStart"] = $roomSet->rangeStart;
                $setRepr["rangeEnd"] = $roomSet->rangeEnd;
            } else {
                $setRepr["name"] = $roomSet->name;
            }
            $result[] = $setRepr;
        }
        return response()->json($result);
    }

    public function getResidenceRoomTypes($confId, $residenceId) {
        if(!Entrust::can(PermissionNames::ConferenceRoomEdit($confId))) {
            return response("", 403);
        }
        $res = Residence::find($residenceId);
        if (is_null($res) || $res->conferenceID != $confId) {
            return response("", 404);
        }
        return $res->roomSets->type()->distinct()->get();
    }

    public function createRoomSet(Request $request, $confId, $residenceId) {
        if(!Entrust::can(PermissionNames::ConferenceRoomEdit($confId))) {
            return response("", 403);
        }
        return DB::transaction(function() use ($request, $confId, $residenceId) {
            $res = Residence::find($residenceId);
            if (is_null($res) || $res->conferenceID != $confId) {
                return response("", 404);
            }

            $this->validateRoomSet($request);
            if ($request->has('typeID')) {
                $type = $request->input('typeID');
            } else {
                $tyVal = new RoomType;
                $tyVal->name = $request->input('type.name');
                $tyVal->capacity = $request->input('type.capacity');
                $tyVal->accessible = $request->input('type.accessible');
                $tyVal->save();
                $type = $tyVal->id;
            }
            $set = new RoomSet;
            $set->typeID = $type;
            $set->residenceID = $residenceId;
            if ($request->has('name')) {
                $set->name = $request->input('name');
            } else {
                $set->rangeStart = $request->input('rangeStart');
                $set->rangeEnd = $request->input('rangeEnd');
            }
            $set->save();
            return response()->json(['id' => $set->id, 'typeID' => $type]);
        });
    }

    private function validateResidence($req) {
        $this->validate($req, [
            'name' => 'required|string',
            'location' => 'required|string'
        ]);
    }

    private function validateRoomSet($req) {
        $this->validate($req, [
            'name' => 'required_without:rangeStart,rangeEnd|string',
            'rangeStart' => 'required_without:name',
            'rangeEnd' => 'required_without:name',
            'typeID' => 'numeric',
            'type.name' => 'required_without:typeID|string',
            'type.capacity' => 'required_without:typeID|numeric',
            'type.accessible' => 'required_without:typeID|boolean'
        ]);
    }
}
