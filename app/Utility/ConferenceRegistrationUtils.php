<?php

namespace App\Utility;

use App\UserConference;
use App\UserEvent;
use App\User;
use App\UserInventory;
use Auth;

class ConferenceRegistrationUtils {
    public static function getAccountRegistrationData($conferenceId, $account = null) {
        if (is_null($account)) {
            $account = Auth::user();
        }

        //If no user is logged in, return an empty list
        if (is_null($account)) {
            return [];
        }

        $accountId = $account->id;

        $attendances =
            UserConference::where("conferenceID", $conferenceId)
                ->whereHas("user", function($query) use ($accountId){
                    $query->where("accountID", $accountId);
                })
                ->get();

        $attendees = [];
        foreach ($attendances as $a) {
            $attendanceInventory = UserInventory::with('inventory')
                ->whereHas('inventory', function ($query) use ($conferenceId) {
                    $query->where('conferenceID', $conferenceId);
                })->where('userID', $a->userID)->get()->toArray();
            $attendees[] = [
                "user" => $a->userID,
                "userData" => $a->user,
                "status" => $a->approved ? "approved" : "pending",
                "id" => $a->id,
                "flight" => $a->flight,
                "room" => $a->room()->with('roomSet.residence', 'roomSet.type')->get()->toArray(),
                "userTransportation" =>  $a->userTransportation()->with('transportation')->get()->toArray(),
                "userInventory" => $attendanceInventory];
        }

        return $attendees;
    }

    public static function getAccountEventRegistrationData($evtId, $account = null) {
        if(is_null($account)) {
            $account = Auth::user();
        }

        //No user logged in
        if (is_null($account)) {
            return [];
        }

        $attendances =
            UserEvent::where('eventID', $evtId)
                ->whereHas('user', function($query) use ($account) {
                    $query->where('accountID', $account->id);
                })->get();

        $attendees = [];
        foreach ($attendances as $a) {
            $attendees[] = [
                "userId" => $a->userID,
                "registrationId" => $a->id];
        }

        return $attendees;
    }
}
