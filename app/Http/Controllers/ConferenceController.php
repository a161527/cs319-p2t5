<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use DB;
use Illuminate\Database\QueryException;
use JWTAuth;

class ConferenceController extends Controller
{

    public function __construct() {
    }

    private function validateConferenceJson($req) {
        $this->validate($req, [
            'name' => 'required',
            'start' => 'required',
            'end' => 'required',
            'location' => 'required' ]);
    }
    private function jsonReqAsDBArray($req) {
        return
            ['conferenceName' => $req->input('name'),
             'dateStart' => $req->input('start'),
             'dateEnd' => $req->input('end'),
             'location' => $req->input('location')];
    }

    private function conferenceResponseJSONArray($conference) {
        return [
                'id' => (int)$conference->id,
                'name' => $conference->conferenceName,
                'start' => $conference->dateStart,
                'end' => $conference->dateEnd,
                'location' => $conference->location];
    }
    public function createNew(Request $req) {
        $this->validateConferenceJson($req);
        $id = DB::table('conferences')->insertGetId($this->jsonReqAsDBArray($req));
        return response()->json(['id' => (int)$id]);
    }

    public function getInfo($id) {
        $rows = DB::table('conferences')->where('id', $id)->get();
        if (sizeof($rows) > 1) {
            return response("Multiple conferences match the ID?", 500);
        }

        if (sizeof($rows) == 0) {
            return response("No conference for id {$id}.", 404);
        }

        $conference = $rows[0];
        return response()->json($this->conferenceResponseJSONArray($conference));
    }

    public function getInfoList() {
        $rows = DB::table('conferences')->get();
        $jsonArrays =
            array_map(
                function($x) { return $this->conferenceResponseJSONArray($x); },
                $rows);
        return response()->json($jsonArrays);
    }

    public function replace(Request $req, $id) {
        $this->validateConferenceJson($req);
        DB::table('conferences')
            ->where('id', $id)
            ->update($this->jsonReqAsDBArray($req));
        return '';
    }

    public function delete($id) {
        DB::table('conferences')->where('id', $id)->delete();
        return '';
    }
}
