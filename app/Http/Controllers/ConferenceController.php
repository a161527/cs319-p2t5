<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use DB;
use Illuminate\Database\QueryException;
use JWTAuth;
use App\Conference;

class ConferenceController extends Controller
{

    public function __construct() {
    }

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

    private function conferenceResponseJSONArray($conference) {
        return [
                'id' => (int)$conference->id,
                'name' => $conference->conferenceName,
                'start' => $conference->dateStart,
                'end' => $conference->dateEnd,
                'location' => $conference->location,
                'description' => $conference->description,
                'hasTransportation' => $conference->hasTransportation,
                'hasAccommodations' => $conference->hasAccommodations];
    }

    private function assignInputToConference($req, $conf) {
        $conf->conferenceName = $req->input('name');
        $conf->dateStart = $req->input('start');
        $conf->dateEnd = $req->input('end');
        $conf->location = $req->input('location');
        $conf->description = $req->input('description');
        $conf->hasTransportation = $req->input('hasTransportation');
        $conf->hasAccommodations = $req->input('hasAccommodations');
    }

    public function createNew(Request $req) {
        $this->validateConferenceJson($req);

        $conf = new Conference;
        $this->assignInputToConference($req, $conf);
        $conf->save();

        return response()->json(['id' => (int)$conf->id]);
    }

    public function getInfo($id) {
        $conference = Conference::find($id);

        if (is_null($conference)) {
            return response("No conference for id {$id}.", 404);
        }

        return response()->json($this->conferenceResponseJSONArray($conference));
    }

    public function getInfoList() {
        $conferences = Conference::all();

        $jsonArrays = [];
        foreach ($conferences as $conf) {
            array_push($jsonArrays, $this->conferenceResponseJSONArray($conf));
        }
        return response()->json($jsonArrays);
    }

    public function replace(Request $req, $id) {
        $this->validateConferenceJson($req);
        $conf = Conference::find($id);

        if (is_null($conf)) {
            return response("Conference {$id} does not exist.", 400);
        }
        $this->assignInputToConference($req, $conf);
        $conf->save();
        return '';
    }

    public function delete($id) {
        Conference::destroy($id);
        return '';
    }
}
