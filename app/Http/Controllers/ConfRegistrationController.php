<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use DB;
use JWTAuth;
class BadDependentList extends \Exception {
    public $response;

    public function __construct($response) {
        $this->response = $response;
    }
}


class ConfRegistrationController extends Controller
{

    public function __construct() {
        $this->middleware('jwt.auth');
    }

    private function dependentsAreOkay($userID, $dependentIDList) {
        //TODO FIXME This needs to actually validate dependents
        return false;
    }

    private function addConferenceRegistration($conferenceID, $userID, $flight) {
        return DB::insertGetId([
            'userID' => $userID,
            'conferenceID' => $conferenceID,
            'flightID' => $flight]);
    }

    private function processRegistration($req, $conferenceID, $userID) {
        DB::transaction(function () use ($req, $conferenceID, $userID){
            $number = $req->input('flight.number');
            $departure = $req->input('flight.departure');
            $arrival = $req->input('flight.arrival');
            $airline = $req->input('flight.airline');
            if(!$this->dependentsAreOkay($userID, $req->input('dependents'))) {
                throw new BadDependentList(response("Dependent(s) not owned by user", 403));
            }
            $result =
                DB::table('flights')
                    ->where('flightNumber', $number)
                    ->where('airline', $airline)
                    ->get();
            if(sizeof($result) >= 1) {
                $row = $result[0];
                if ($row->departureTime != $departure || $row->arrivalTime != $arrival) {
                    return $this->addRegistration($conferenceID, $userID, $row->id);
                } else {
                    return response("Flight data does not match known data for flight", 400);
                }
            } else {
                $newID = DB::table('flights')->insertGetId([
                    'flightNumber' => $number,
                    'departureTime' => $departure,
                    'arrivalTime' => $arrival,
                    'airline' => $airline
                ]);
                return $this->addRegistration($conferenceID, $userID, $newID);
            }
        });
    }

    private function validateRegistrationRequest($request) {
        $this->validate($request, [
            'dependents' => 'required',
            'flight.number' => 'numeric|required',
            'flight.departure' => 'date_format:Y-m-d H:i:s|required',
            'flight.arrival' => 'date_format:Y-m-d H:i:s|required',
            'flight.airline' => 'required'
        ]);
    }

    public function userRegistration(Request $req, $conferenceID) {
        $this->validateRegistrationRequest($req);
        $user = JWTAuth::parseToken()->authenticate();
        try {
            return $this->processRegistration($req, $conferenceID, $user->id);
        } catch (BadDependentList $badDeps) {
            return $badDeps->response;
        }
    }
}
