<?php

namespace App\Utility;

use App\UserConference;
use App\User;
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
            $attendees[] = [
                "user" => $a->userID,
                "status" => $a->approved ? "approved" : "pending",
                "id" => $a->id];
        }

        return $attendees;
    }
}
