<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\User;

class UserController extends Controller
{
    public function index($accountId)
    {
    	$account = Account::where('id', '=', $accountId)->first();
		if ($account === null)
			return response()->json(['message' => 'account_not_found']);
		else
		{
			$dependents = User::where('accountID', '=', $account->id)->get();
			// dd($dependents);
			return response()->json(['dependents' => $dependents]);
		}
    }
}
