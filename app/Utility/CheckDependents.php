<?php

namespace App\Utility;

use App\User;

use Auth;

class CheckDependents {

    //Checks whether the list of dependents is valid for the user specified by
    //accountID.  (This essentially means checking whether the dependents are associated
    //with that account)
    public static function dependentsOkay($dependentIDList) {
        $accountID = Auth::user()->id;
        $matchCount =
            User::whereIn('id', $dependentIDList)
                ->where(function ($query) use ($accountID) {
                    $query->where('accountId', '=', $accountID)
                          ->where('approved', '<>', 'false');
                })->count();
        return $matchCount == sizeof($dependentIDList);
    }
}
