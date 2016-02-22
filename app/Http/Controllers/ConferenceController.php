<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use DB;
use Illuminate\Database\QueryException;

class ConferenceController extends Controller
{

    private function validateConferenceJson($req) {
        $this->validate($req, [
            'name' => 'required',
            'start' => 'required',
            'end' => 'required',
            'location' => 'required' ]);
    }
    private function jsonReqAsDBArray($req) {
        return
            ['ConferenceName' => $req->input('name'),
             'DateStart' => $req->input('start'),
             'DateEnd' => $req->input('end'),
             'Location' => $req->input('location')];
    }

    private function conferenceResponseJSONArray($conference) {
        return [
                'id' => (int)$conference->id,
                'name' => $conference->ConferenceName,
                'start' => $conference->DateStart,
                'end' => $conference->DateEnd,
                'location' => $conference->Location];
    }
    public function createNew(Request $req) {
        $this->validateConferenceJson($req);
        $id = DB::table('conference')->insertGetId($this->jsonReqAsDBArray($req));
        return response()->json(['id' => (int)$id]);
    }

    public function getInfo($id) {
        $rows = DB::table('conference')->where('id', $id)->get();
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
        $rows = DB::table('conference')->get();
        $jsonArrays =
            array_map(
                function($x) { return $this->conferenceResponseJSONArray($x); },
                $rows);
        return response()->json($jsonArrays);
    }

    public function replace(Request $req, $id) {
        $this->validateConferenceJson($req);
        try {
            DB::table('conference')
                ->where('id', $id)
                ->update($this->jsonReqAsDBArray($req));
        } catch (QueryException $qe) {
            return response('', 500);
        }
        return '';
    }

    public function delete($id) {
        try {
            DB::table('conference')->where('id', $id)->delete();
        } catch (QueryException $qe) {
            return response('', 500);
        }
        return '';
    }
}
