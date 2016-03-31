<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Conference;
use App\Event;
use App\UserConference;

use Response;

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
        } else {
            $this->filename = $conf->name . "_" . $reportName;
        }
        switch ((string) $reportName) {
            case "ConferenceRegistration.csv":
                return $this->generateConferenceRegistrationReport($confId);
            default:
                return response()->json(["message" => "no_such_conference_report"], 404);
        }
    }

    private function handleEventReport($confId, $evtId, $reportName) {
        $evt = Event::find($evtId);
        if (is_null($evt) || $evt->conferenceID != $confId) {
            return response()->json(["message" => "report_target_event_not_found"], 404);
        }

        switch ((string)  $reportName) {
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
                is_null($u->room) ? "" : $u->room->name,
                $u->needsTransportation,
                is_null($u->userTransportation) ? "" : $u->userTransportation->transportation->name,
                is_null($u->userTransportation) ? "" : $u->userTransportation->transportation->company,
                is_null($u->flightID) ? "" : $u->flight->airline . " " . $u->flight->flightNumber,
                is_null($u->flightID) ? "" : $u->flight->airport,
                is_null($u->flightID) ? "" : $u->flight->arrivalDate,
                is_null($u->flightID) ? "" : $u->flight->arrivalTime
            ];
        }
        $this->writeCSVResponse($data);
    }

    private function writeCSVResponse($dataArray) {
        $fname = is_null($this->filename) ? "report.csv" : $this->filename;
        $headers = [
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename=' . $fname,
            'Expires'             => '0',
            'Pragma'              => 'public'
        ];

        $cb = function () use ($dataArray) {
            $handle = fopen("php://output", "w");
            print_r($dataArray);
            foreach ($dataArray as $row) {
                fputcsv($handle, $row);
            }
            fclose($handle);
        };
        return Response::stream($cb, 200, $headers);
    }
}
