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

use Validator;

use Illuminate\Foundation\Validation\ValidationException;

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

    public function getResidenceInfo($confId, $resId) {
        if (!Entrust::can(PermissionNames::ConferenceRoomEdit($confId))) {
            return response("", 403);
        }

        $result = Residence::where("conferenceID", $confId)->find($resId);
        if(!isset($result)) {
            return response("", 404);
        }

        return $result;
    }

    public function uploadRoomData($confId) {
        return "NOTIMPLEMENTED";
    }

    public function createResidence(Request $req, $confId) {
        if (!Entrust::can(PermissionNames::ConferenceRoomEdit($confId))) {
            return response("", 403);
        }
        $responses = [];
        foreach ($req->all() as $request) {
            $this->validateResidence($request);
            $residence = new Residence;
            $residence->name = $request['name'];
            $residence->location = $request['location'];
            $residence->conferenceID = $confId;
            $residence->save();

            $responses[] = ["id" => $residence->id, "name" => $residence->name];
        }

        return response()->json($responses);
    }

    public function getResidenceRoomSets($confId, $residenceId) {
        if (!Entrust::can(PermissionNames::ConferenceRoomEdit($confId))) {
            return response("", 403);
        }

        $res = Residence::with("roomSets.type")->find($residenceId);
        if (is_null($res) || $res->conferenceID != $confId) {
            return response("", 404);
        }

        $result = [];
        foreach ($res->roomSets as $roomSet) {
            $setRepr = ["type" => $roomSet->type->toArray(),
                        "id" => $roomSet->id];
            $setRepr["id"] = $roomSet->id;
            $setRepr["name"] = $roomSet->name;
            $result[] = $setRepr;
        }
        return response()->json($result);
    }

    public function getResidenceRoomTypes($confId, $residenceId) {
        if(!Entrust::can(PermissionNames::ConferenceRoomEdit($confId))) {
            return response("", 403);
        }
        $res = Residence::with("roomSets.type")->find($residenceId);
        if (is_null($res) || $res->conferenceID != $confId) {
            return response("", 404);
        }

        return RoomType::whereHas("roomSets", function ($query) use ($res){
            $query->where("residenceID", $res->id);
        })->get();
    }

    public function createRoomSet(Request $req, $confId, $residenceId) {
        if(!Entrust::can(PermissionNames::ConferenceRoomEdit($confId))) {
            return response("", 403);
        }
        return DB::transaction(function() use ($req, $confId, $residenceId) {
            $res = Residence::find($residenceId);
            if (is_null($res) || $res->conferenceID != $confId) {
                return response("", 404);
            }

            $responses = [];
            foreach ($req->all() as $request) {
                $this->validateRoomSet($request);
                if (isset($request['typeID'])) {
                    $type = $request['typeID'];
                } else {
                    $tyVal = new RoomType;
                    $tyVal->name = $request['type.name'];
                    $tyVal->capacity = $request['type.capacity'];
                    $tyVal->accessible = $request['type.accessible'];
                    $tyVal->save();
                    $type = $tyVal->id;
                }
                $set = new RoomSet;
                $set->typeID = $type;
                $set->residenceID = $residenceId;
                $set->name = $request['name'];
                $set->save();
                $responses[] = ['name' => $set->name, 'id' => $set->id, 'typeID' => $type];
            }
            return response()->json($responses);
        });
    }

    public function getRoomSetInfo(Request $req, $confId, $setId) {
        if(!Entrust::can(PermissionNames::ConferenceRoomEdit($confId))) {
            return response("", 403);
        }

        $set = RoomSet::with('type')->whereHas('residence', function ($q) use ($confId) {
            $q->where('conferenceID', $confId);
        })->find($setId);

        if (!isset($set)) {
            return response("", 404);
        }

        return $set;
    }

    private function validateResidence($req) {
        $v = Validator::make($req, [
            'name' => 'required|string',
            'location' => 'required|string'
        ]);
        if ($v->fails()) {
            throw new ValidationException($v);
        }
    }

    private function validateRoomSet($req) {
        $v = Validator::make($req, [
            'name' => 'required|string',
            'typeID' => 'numeric',
            'type.name' => 'required_without:typeID|string',
            'type.capacity' => 'required_without:typeID|numeric',
            'type.accessible' => 'required_without:typeID|boolean'
        ]);

        if ($v->fails()) {
            throw new ValidationException($v);
        }
    }
}
