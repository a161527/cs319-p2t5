<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use DB;
use JWTAuth;

use App\Flight;

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

    private function addConferenceRegistration($conferenceID, $userID, $needsTransportation, $flight) {
        $userConf = new UserConference;
        $userConf->userID = $userID;
        $userConf->conferenceID = $conferenceID;
        $userConf->needsTransportation = $needsTransportation;
        $userConf->approved = false;
        $userConf->flightId = $flight;

        $userConf->save();
        return response()->json(['id' => $userConf->id]);
    }

    private function processRegistration($req, $conferenceID, $userID) {
        DB::transaction(function () use ($req, $conferenceID, $userID){
            $number = $req->input('flight.number');
            $arrivalDay = $req->input('flight.arrivalDate');
            $arrivalTime = $req->input('flight.arrivalTime');
            $airline = $req->input('flight.airline');

            $needsTransport = $req->input('needsTransportation');

            if(!$this->dependentsAreOkay($userID, $req->input('dependents'))) {
                throw new BadDependentList(response("Dependent(s) not owned by user", 403));
            }

            if ($req->has('hasFlight')) {
                if (!$req->input('hasFlight')) {
                   return $this->addConferenceRegistration($conferenceID, $userID, $needsTransport, null);
                }
            }

            $flights =
                Flight::where('flightNumber', $number)
                ->where('airline', $airline)
                ->where('arrivalDate', $arrivalDay)
                ->where('isChecked', true)
                ->get();

            if(sizeof($flights) >= 1) {
                foreach($flights as $row) {
                    if ($row->arrivalTime == $arrivalTime) {
                        return $this->addRegistration($conferenceID, $userID, $needsTransport, $row->id);
                    } else {
                        return response("Flight data does not match known data for flight", 400);
                    }
                }
            } else {
                $airport = $req->input('flight.airport');

                $flight = new Flight;
                $flight->flightNumber = $number;
                $flight->airline = $airline;
                $flight->arrivalDate = $arrivalDay;
                $flight->arrivalTime = $arrivalTime;
                $flight->airport = $airport;
                $flight->isChecked = false;

                $flight->save();
                return $this->addRegistration($conferenceID, $userID, $needsTransport, $flight->id);
            }
        });
    }

    private function validateRegistrationRequest($request) {
        $this->validate($request, [
            'dependents' => 'required',
            'needsTransportation' => 'required|boolean',
            'hasFlight' => 'boolean',
            'flight.number' => 'numeric|required_unless:hasFlight,false',
            'flight.arrivalDate' => 'date_format:Y-m-d|required_unless:hasFlight,false',
            'flight.arrivalTime' => 'date_format:H:i|required_unless:hasFlight,false',
            'flight.airline' => 'required_unless:hasFlight,false|string',
            'flight.airport' => 'required_unless:hasFlight,false|string'
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
