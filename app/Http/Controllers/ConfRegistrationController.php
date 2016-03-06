<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use DB;
use JWTAuth;

use App\Flight;
use App\User;
use App\UserConference;

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

    private function dependentsAreOkay($accountID, $dependentIDList) {
        foreach ($dependentIDList as $attendee) {
            $matchCount = User::where('id', $attendee)->where('accountId', $accountID)->count();
            if ($matchCount < 1) {
                return false;
            }
        }
        return true;
    }

    private function registerAttendees($confID, $users, $needsTransport, $flight) {
        $registryIDs = [];
        foreach ($users as $attendee) {
            $newRegistryID = $this->addConferenceRegistration($confID, $attendee, $needsTransport, $flight);
            $registryIDs[] = $newRegistryID;
        }
        return response()->json(['ids' => $registryIDs]);
    }

    private function addConferenceRegistration($conferenceID, $userID, $needsTransportation, $flight) {
        $userConf = new UserConference;
        $userConf->userID = $userID;
        $userConf->conferenceID = $conferenceID;
        $userConf->needsTransportation = $needsTransportation;
        $userConf->approved = false;
        $userConf->flightID = $flight;

        $userConf->save();
        return $userConf->id;
    }

    private function processRegistration($req, $conferenceID, $accountID) {
        return DB::transaction(function () use ($req, $conferenceID, $accountID){
            $number = $req->input('flight.number');
            $arrivalDay = $req->input('flight.arrivalDate');
            $arrivalTime = $req->input('flight.arrivalTime');
            $airline = $req->input('flight.airline');

            $needsTransport = $req->input('needsTransportation');
            $attendees = $req->input('attendees');

            if(!$this->dependentsAreOkay($accountID, $attendees)) {
                throw new BadDependentList(response("Dependent(s) not owned by user", 403));
            }

            if ($req->has('hasFlight')) {
                if (!$req->input('hasFlight')) {
                   return $this->registerAttendees($conferenceID, $attendees, $needsTransport, null);
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
                        return $this->registerAttendees($conferenceID, $attendees, $needsTransport, $row->id);
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
                return $this->registerAttendees($conferenceID, $attendees, $needsTransport, $flight->id);
            }
        });
    }

    private function validateRegistrationRequest($request) {
        $this->validate($request, [
            'attendees' => 'required|idarray',
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

    //This should validate whether the currently logged in user is
    //a registration approver for the given conference.  We don't
    //have these permissions yet though.
    private function isUserRegistrationApprover($conferenceID) {
        return true;
    }
    public function approveRegistration($conferenceID, $requestID) {
        if(!$this->isUserRegistrationApprover($conferenceID)) {
            return response("", 401);
        }
        DB::transaction(function () use ($requestID) {
            $conference = UserConference::find($requestID);
            if ($conference != null && $conference->approved = false) {
                $conference->approved = true;
                $conference->save();
            }

            $flight = $conference->flight;
            if (!$flight->isChecked) {
                $flight->isChecked = true;
                $flight->save();
            }
        });
    }
}
