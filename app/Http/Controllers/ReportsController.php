<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Conference;
use App\Event;
use App\UserConference;
use App\UserInventory;
use App\UserRoom;
use App\UserEvent;
use App\Transportation;
use App\Flight;

use Response;
use Log;
use Entrust;
use App\Utility\PermissionNames;

class ReportsController extends Controller
{
    private $filename = null;

    public function __construct() {
        $this->middleware('jwt.auth.rejection');
    }

    public function getReport($reportName) {
        $parts = explode("_", $reportName);
        if (sizeof($parts) == 1) {
            return response()->json(["message" => "no_report_parameter_found"], 404);
        }

        if (sizeof($parts) == 2) {
            return $this->handleConferenceReport($parts[0], $parts[1]);
        }

        if (sizeof($parts) == 3) {
            return $this->handleEventReport($parts[0], $parts[1], $parts[2]);
        }
    }

    private function handleConferenceReport($confId, $reportName) {
        $conf = Conference::find($confId);
        if (is_null($conf)) {
            return response()->json(["message" => "report_target_conference_not_found"], 404);
        }

        if (!Entrust::can(PermissionNames::ConferenceViewReports($confId))) {
            return response()->json(["message" => "report_not_accessible"], 403);
        }

        $this->filename = $conf->conferenceName . "_" . $reportName;

        switch ((string) $reportName) {
            case "ConferenceRegistration.csv":
                return $this->generateConferenceRegistrationReport($confId);
            case "AssignedInventory.csv":
                return $this->generateAssignedInventoryReport($confId);
            case "ConferenceDemographics.csv":
                return $this->generateConferenceDemographicsReport($confId);
            case "TransportationSchedule.csv":
                return $this->generateTransportationScheduleReport($confId);
            default:
                return response()->json(["message" => "no_such_conference_report"], 404);
        }
    }

    private function handleEventReport($confId, $evtId, $reportName) {
        $evt = Event::with('conference')->find($evtId);
        if (is_null($evt) || $evt->conferenceID != $confId) {
            return response()->json(["message" => "report_target_event_not_found"], 404);
        }

        if (!Entrust::can(PermissionNames::EventDetailView($evtId))) {
            return response()->json(["message" => "report_not_accessible"], 403);
        }

        $this->filename = $evt->conference->conferenceName . "_" . $evt->eventName . "_" . $reportName;

        switch ((string)  $reportName) {
            case "EventRegistration.csv":
                return $this->generateEventRegistrationReport($evtId);
            case "EventDemographics.csv":
                return $this->generateEventDemographicsReport($evtId);
            default:
                return response()->json(["message" => "no_such_event_report"], 404);
        }
    }

    private function generateConferenceRegistrationReport($confId) {
        $users = UserConference::where('conferenceID', $confId)
                    ->where('approved', true)
                    ->with('room.roomSet.residence', 'userTransportation.transportation', 'user')
                    ->get();

        $data = [];
        foreach ($users as $u) {
            $data[] = [
                $u->user->firstName . " " . $u->user->lastName,
                $u->needsAccommodation ? "true" : "false",
                is_null($u->room) ? "" : $u->room->roomSet->residence->name,
                is_null($u->room) ? "" : $u->room->roomSet->name,
                is_null($u->room) ? "" : $u->room->roomName,
                $u->needsTransportation,
                is_null($u->userTransportation) ? "" : $u->userTransportation->transportation->name,
                is_null($u->userTransportation) ? "" : $u->userTransportation->transportation->company,
                is_null($u->flightID) ? "" : $u->flight->airline . " " . $u->flight->flightNumber,
                is_null($u->flightID) ? "" : $u->flight->airport,
                is_null($u->flightID) ? "" : $u->flight->arrivalDate,
                is_null($u->flightID) ? "" : $u->flight->arrivalTime
            ];
        }
        return $this->writeCSVResponse($data);
    }

    private function generateAssignedInventoryReport($confId) {
        $assigned = UserInventory::whereHas('inventory', function ($query) use ($confId) {
            $query->where('conferenceID', $confId);
        })->where('approved',true)
        ->with('inventory')
        ->with(['user.registrations' => function ($query) use ($confId){
            $query->where('conferenceID', $confId);
            $query->with('room.roomSet.residence');
        }])->get();

        $data = [];
        foreach ($assigned as $a) {
            $uconf = $a->user->registrations->first();
            $data[] = [
                $a->user->firstName . " " . $a->user->lastName,
                is_null($uconf->room) ? "" : $uconf->room->roomSet->residence->name,
                is_null($uconf->room) ? "" : $uconf->room->roomSet->name,
                is_null($uconf->room) ? "" : $uconf->room->roomName,
                $a->unitCount,
                $a->inventory->itemName];
        }

        return $this->writeCSVResponse($data);
    }

    private function generateConferenceDemographicsReport($confId) {
        $attendees = UserConference::where('conferenceID', $confId)->with('user')->get();

        $data = [];
        foreach ($attendees as $a) {
            $u = $a->user;
            $data[] = [
                $u->firstName . " " . $u->lastName,
                $u->dateOfBirth,
                $u->gender,
                $u->location
            ];
        }

        return $this->writeCSVResponse($data);
    }

    private function generateTransportationScheduleReport($confId) {
        $transports = Transportation::where('conferenceID', $confId)->with('userTransportations')->get();

        $data = [];
        foreach ($transports as $t) {
            $flights = Flight::whereHas('userConferences', function ($query) use ($t) {
                $query->whereHas('userTransportation', function ($query) use ($t) {
                    $query->where('id', $t->id);
                });
            })->get();

            foreach ($flights as $f) {
                $data[] = [
                    $t->name,
                    $t->company,
                    $t->phone,
                    $f->userCount,
                    $f->airport,
                    $f->airline,
                    $f->arrivalDate,
                    $f->arrivalTime];
            }
        }

        return $this->writeCSVResponse($data);
    }

    private function generateEventRegistrationReport($eventId) {
        $attendance = UserEvent::where('eventID', $eventId)->with('user')->get();

        $data = [];
        foreach ($attendance as $a) {
            $data[] = [
                $a->user->firstName . " " . $a->user->lastName
            ];
        }

        return $this->writeCSVResponse($data);
    }

    private function generateEventDemographicsReport($eventId) {
        $attendance = UserEvent::where('eventID', $eventId)->with('user', 'event')->get();

        $data = [];
        foreach($attendance as $a) {
            $uniqueCount = UserEvent::where('userID', $a->userID)
                ->whereHas('event', function ($query) use ($a){
                    $query->where('conferenceID', $a->event->conferenceID);
                })->count();
            $u = $a->user;

            $data[] = [
                $u->firstName . " " . $u->lastName,
                $u->dateOfBirth,
                $u->gender,
                $u->location,
                $uniqueCount];
        }

        return $this->writeCSVResponse($data);
    }

    private function writeCSVResponse($dataArray) {
        $fname = is_null($this->filename) ? "report.csv" : $this->filename;
        $headers = [
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $fname . '"',
            'Expires'             => '0',
            'Pragma'              => 'public'
        ];

        $cb = function () use ($dataArray) {
            Log::info("Running callback for CSV report");
            $handle = fopen("php://output", "w");
            foreach ($dataArray as $row) {
                fputcsv($handle, $row);
            }
            fclose($handle);
        };

        return response()->stream($cb,200,$headers);
    }
}
