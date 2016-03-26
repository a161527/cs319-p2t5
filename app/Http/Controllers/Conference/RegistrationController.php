<?php

namespace App\Http\Controllers\Conference;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use DB;
use JWTAuth;
use Validator;

use App\Flight;
use App\User;
use App\UserConference;
use Auth;
use Entrust;
use App\Utility\PermissionNames;
use App\Utility\CheckDependents;

use App\Jobs\RegistrationFlightAggregator;

use Illuminate\Foundation\Validation\ValidationException;
/**
 * Controller for handling conference registration.
 *
 * Handles both registration applications by users and approval of these
 * applications by admins.  This results in handling of flight data as well,
 * as flights are created in the DB as registrations are created.
 */
class RegistrationController extends Controller
{
    //Types of access to a registration application
    const REGISTRATION_FULL_ACCESS_TYPE = "full";
    const REGISTRATION_EDIT_ACCESS_TYPE = "edit";

    public function __construct() {
        $this->middleware('jwt.auth');
    }

    /*
     * Registers a group of attendees for a conference.
     */
    private function registerAttendees($confID, $users, $needsTransport, $needsAccommodation, $flight) {
        $registryIDs = [];
        foreach ($users as $attendee) {
            $newRegistryID = $this->addConferenceRegistration($confID, $attendee, $needsTransport, $needsAccommodation, $flight);
            $registryIDs[] = $newRegistryID;
        }
        return ['ids' => $registryIDs, "attendees" => $users, "code" => 200];
    }

    /*
     * Registers a single attendee for a conference
     */
    private function addConferenceRegistration($conferenceID, $userID, $needsTransportation, $needsAccommodation, $flight) {
        $userConf = new UserConference;
        $userConf->userID = $userID;
        $userConf->conferenceID = $conferenceID;
        $userConf->needsTransportation = $needsTransportation;
        $userConf->needsAccommodation = $needsAccommodation;
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
            $needsTransport = $req['needsTransportation'];
            $needsAccommodation = $req['needsAccommodation'];
            $attendees = $req['attendees'];

            //Check whether dependents are okay/owned by the current user
            if(!CheckDependents::dependentsOkay($attendees)) {
                return ["message" => "bad_attendee_listing", "code" => 400, "attendees" => $attendees];
            }

            $existing = UserConference::where('conferenceID', $conferenceID)
                        ->whereIn('userID', $attendees)->count();
            if ($existing > 0) {
                return ["message" => "already_registered", "code" => 400, "attendees" => $attendees];
            }

            //If the request explicitly doesn't have a flight, just register attendees without one
            if (isset($req['hasFlight'])) {
                if (!$req['hasFlight']) {
                   return $this->registerAttendees($conferenceID, $attendees, $needsTransport, $needsAccommodation, null);
                }
            }

            $number = $req['flight']['number'];
            $arrivalDay = $req['flight']['arrivalDate'];
            $arrivalTime = $req['flight']['arrivalTime'];
            $airline = $req['flight']['airline'];
            $airport = $req['flight']['airport'];

            //Grab (checked) matching flights for airline/flight number/date
            $flights =
                Flight::where('flightNumber', $number)
                ->where('airline', $airline)
                ->where('arrivalDate', $arrivalDay)
                ->where('isChecked', true)
                ->get();

            if(sizeof($flights) >= 1) {
                foreach($flights as $row) {
                    if ($row->arrivalTime == $arrivalTime && $row->airport == $airport) {
                        return $this->registerAttendees($conferenceID, $attendees, $needsTransport, $needsAccommodation, $row->id);
                    }
                }
                return ["message" => "flight_data_mismatch", "code" => 400, "attendees" => $attendees];
            } else {
                //Create a new flight with the given data

                $flight = new Flight;
                $flight->flightNumber = $number;
                $flight->airline = $airline;
                $flight->arrivalDate = $arrivalDay;
                $flight->arrivalTime = $arrivalTime;
                $flight->airport = $airport;
                $flight->isChecked = false;

                $flight->save();
                return $this->registerAttendees($conferenceID, $attendees, $needsTransport, $needsAccommodation, $flight->id);
            }
        });
    }

    private function validateRegistrationRequest($request) {
        $v = Validator::make($request, [
            'attendees' => 'required|idarray',
            'needsTransportation' => 'required|boolean',
            'needsAccommodation' => 'required|boolean',
            'hasFlight' => 'boolean',
            'flight.number' => 'numeric|required_unless:hasFlight,false',
            'flight.arrivalDate' => 'date_format:Y-m-d|required_unless:hasFlight,false',
            'flight.arrivalTime' => 'date_format:H:i:s|required_unless:hasFlight,false',
            'flight.airline' => 'required_unless:hasFlight,false|string',
            'flight.airport' => 'required_unless:hasFlight,false|string'
        ]);

        if ($v->fails()) {
            throw new ValidationException($v);
        }
    }

    public function userRegistration(Request $req, $conferenceID) {
        $user = Auth::user();

        $data = [];

        foreach ($req->all() as $request) {
            $this->validateRegistrationRequest($request);
        }
        foreach ($req->all() as $request) {
            $data[] = $this->processRegistration($request, $conferenceID, $user->id);
        }

        //Ugly, but works - go through the results.  If we find one that isn't okay (200)
        //then use that as the response code.  Otherwise, we finish the loop and just
        //return a 200 status code.
        foreach ($data as $d) {
            if ($d["code"] != 200) {
                return response()->json($data, $d["code"]);
            }
        }

        return response()->json($data);
    }

    //This should validate whether the currently logged in user is
    //a registration approver for the given conference.  We don't
    //have these permissions yet though.
    private function isUserRegistrationApprover($conferenceID) {
        return Entrust::can(PermissionNames::ConferenceRegistrationApproval($conferenceID));
    }

    /**
     * Handles approval request. If successful, this changes the specified
     * request to approved, as well as setting the flight as checked if necessary.
     */
    public function approveRegistration($conferenceID, $requestID) {
        //Check whether the current user is allowed to do this
        if(!$this->isUserRegistrationApprover($conferenceID)) {
            return response("", 403);
        }

        $success = DB::transaction(function () use ($conferenceID, $requestID) {
            $conference = UserConference::find($requestID);
            if(!isset($conference) || $conferenceID != $conference->conferenceID) {
                return false;
            }
            //Set conference attendance to approved
            if (!is_null($conference) && $conference->approved == false) {
                $conference->approved = true;
                $conference->save();
            }

            //Also update the flight if there is one
            $flight = $conference->flight;
            if ($flight != null && !$flight->isChecked) {
                $flight->isChecked = true;
                $flight->save();
            }
            return true;
        });
        if ($success) {
            $this->dispatch(new RegistrationFlightAggregator($requestID));
            return response("", 200);
        } else {
            return response("Registration request not found for conference.",  404);
        }
    }

    /*
     * Determines how much access (if any) the current user has for the
     * given conference and registration request.  Approvers for the conference
     * have full access, while owners of the user/dependent for the registration
     * attempt have edit access
     */
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
        $registration = UserConference::with("flight", "user")->find($requestID);
        if ($conferenceID != $registration->conferenceID) {
            return response("Registration request not found for conference.",  404);
        }

        $accessType = $this->determineAccessType($conferenceID, $registration);
        if ($accessType == null) {
            return response("", 403);
        }

        return $registration;
    }

    private function validateRegistrationListingRequest($req) {
        $this->validate($req, [
            "include" => "in:all,pending,approved"
        ]);
    }

    public function outstandingRegistrationRequests(Request $req, $conferenceID) {
        if(!$this->isUserRegistrationApprover($conferenceID)) {
            return response("", 403);
        }

        $this->validateRegistrationListingRequest($req);

        if($req->has("include")) {
            $include = $req->input("include");
        } else {
            $include = "all";
        }

        $query = UserConference::with("user", "flight")->where("conferenceID", $conferenceID);

        if($include === "pending") {
            $query->where("approved", false);
        }else if ($include == "approved") {
            $query->where("approved", true);
        }

        return $query->get();
    }
}
