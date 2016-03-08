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
        //Allow info requests without a token.  May need to do extra
        //auth stuff if they want detailed info, but right now we don't
        //make that distinction
        $this->middleware('jwt.auth', ['except' => ['getInfo', 'getInfoList']]);
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

    /**
     * Converts a conference object from the Eloquent object
     * into a json array.
     */
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

        $conf = new Conference;
        $this->assignInputToConference($req, $conf);
        $conf->save();

        return response()->json(['id' => (int)$conf->id]);
    }

    /**
     * Gets info about a specific conference.
     */
    public function getInfo($id) {
        $conference = Conference::find($id);

        if (is_null($conference)) {
            return response("No conference for id {$id}.", 404);
        }

        return response()->json($this->conferenceResponseJSONArray($conference));
    }

    /**
     * Gets a json array with all conferences.
     */
    public function getInfoList() {
        $conferences = Conference::all();

        $jsonArrays = [];
        foreach ($conferences as $conf) {
            array_push($jsonArrays, $this->conferenceResponseJSONArray($conf));
        }
        return response()->json($jsonArrays);
    }

    /**
     * Replaces a given conference with the new values, given valid json.
     */
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

    /**
     * Deletes a conference.
     */
    public function delete($id) {
        Conference::destroy($id);
        return '';
    }
}
