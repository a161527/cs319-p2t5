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
use Auth;

class BadDependentList extends \Exception {
    public $response;

    public function __construct($response) {
        $this->response = $response;
    }
}


class ConfRegistrationController extends Controller
{
    const REGISTRATION_FULL_ACCESS_TYPE = "full";
    const REGISTRATION_EDIT_ACCESS_TYPE = "edit";

    public function __construct() {
        $this->middleware('jwt.auth');
        $this->middleware('auth');
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

    /*
     * Registers a group of attendees for a conference.
     */
    private function registerAttendees($confID, $users, $needsTransport, $flight) {
        $registryIDs = [];
        foreach ($users as $attendee) {
            $newRegistryID = $this->addConferenceRegistration($confID, $attendee, $needsTransport, $flight);
            $registryIDs[] = $newRegistryID;
        }
        return response()->json(['ids' => $registryIDs]);
    }

    /*
     * Registers a single attendee for a conference
     */
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

    /* Process registration for a group of users.
     * This checks that the flight is OK, creates a new (unchecked) flight if
     * needed, and then adds new unapproved UserConference entries as needed.
     */
    private function processRegistration($req, $conferenceID, $accountID) {
        return DB::transaction(function () use ($req, $conferenceID, $accountID){
            //Grab request data for use later
            $number = $req->input('flight.number');
            $arrivalDay = $req->input('flight.arrivalDate');
            $arrivalTime = $req->input('flight.arrivalTime');
            $airline = $req->input('flight.airline');

            $needsTransport = $req->input('needsTransportation');
            $attendees = $req->input('attendees');

            //Check whether dependents are okay/owned by the current user
            if(!$this->dependentsAreOkay($accountID, $attendees)) {
                throw new BadDependentList(response("Dependent(s) not owned by user", 403));
            }

            //If the request explicitly doesn't have a flight, just register attendees without one
            if ($req->has('hasFlight')) {
                if (!$req->input('hasFlight')) {
                   return $this->registerAttendees($conferenceID, $attendees, $needsTransport, null);
                }
            }

            //Grab (checked) matching flights for airline/flight number/date
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
                    }
                }
                return response("Flight data does not match known data for flight", 400);
            } else {
                //Create a new flight with the given data
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
            'flight.arrivalTime' => 'date_format:H:i:s|required_unless:hasFlight,false',
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
        //Check whether the current user is allowed to do this
        if(!$this->isUserRegistrationApprover($conferenceID)) {
            return response("", 401);
        }
        DB::transaction(function () use ($conferenceID, $requestID) {
            $conference = UserConference::find($requestID);
            if ($conferenceID != $conference->conferenceID) {
                return response("Registration request not found for conference.",  404);
            }
            //Set conference attendance to approved
            if ($conference != null && $conference->approved = false) {
                $conference->approved = true;
                $conference->save();
            }

            //Also update the flight if there is one
            $flight = $conference->flight;
            if ($flight != null && !$flight->isChecked) {
                $flight->isChecked = true;
                $flight->save();
            }
        });
    }


    private function determineAccessType($conferenceID, $registration) {
        if ($this->isUserRegistrationApprover($conferenceID)) {
            return self::REGISTRATION_FULL_ACCESS_TYPE;
        } else if (Auth::user()->id == $registration->user->account->id) {
            return self::REGISTRATION_EDIT_ACCESS_TYPE;
        }
        return null;
    }

    /*
     * Get data for a registration.
     */
    public function getRegistrationData(Request $req, $conferenceID, $requestID) {
        $registration = UserConference::find($requestID);
        if ($conferenceID != $registration->conferenceID) {
            return response("Registration request not found for conference.",  404);
        }

        $accessType = $this->determineAccessType($conferenceID, $registration);
        if ($accessType == null) {
            return response("", 401);
        }

        $flightData = $registration->flight->toArray();
        $flightData["number"] = (int) $flightData["flightNumber"];
        unset($flightData["flightNumber"]);

        return response()->json(
            ['needsTransportation' => $registration->needsTransportation,
             'approved' => $registration->approved,
             'attendee' => $registration->user->firstName . " " . $registration->user->lastName,
             'flight' => $flightData,
             'access' => $accessType]);
    }
}
