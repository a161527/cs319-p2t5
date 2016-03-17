<?php

namespace App\Http\Controllers;

use App\Models\Account;
use Validator;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Contracts\Logging\Log;
use DB;

class RegistrationController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Registration Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users.
    |
    */

    use AuthenticatesAndRegistersUsers, ThrottlesLogins;

    /**
     * Create a new authentication controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // $this->middleware('jwt.auth', ['except' => ['authenticate', 'token']]);
        // provides an authorization header with each response
        // $this->middleware('jwt.refresh', ['except' => ['authenticate', 'token']]);
    }

    /**
     * Get a validator for an incoming registration request.
     */
    protected function accountValidator(array $data)
    {
        // TODO: make rules same as front-end validation
    	$validator = Validator::make($data, [
            'email'     =>  'required|email|max:255|unique:accounts',
            'password'  =>  'required|confirmed|min:6|alpha_num',
        ]);

    	return $validator;
    }

    /**
     * Get a validator for an incoming registration request.
     */
    protected function userValidator($data)
    {
        // TODO: make rules same as front-end validation
        $users = json_decode($data["dependents"]);
        $validator = Validator::make($users, [
            '*.firstName' => 'required',
            '*.lastName' => 'required',
            '*.dateOfBirth' => 'required',
            '*.gender' => 'required',
            '*.accountId' => 'required'
        ]);

        return $validator;
    }

    /**
     * Create a new account instance after a valid registration.
     */
    protected function createAccount(Request $request)
    {
        $account = new Account();
        $account->email = $request->email;
        $account->password = Hash::make($request->password);
		$account->save();
    }

    /**
     * Create a new user instance after a valid registration.
     */
    protected function createUsers(Request $request)
    {
        $users = json_decode($request->all()['dependents']);
        foreach ($users as $u)
        {
            $user = new User();
            $user->name = $u->name;
        }
    }


    public function register(Request $request) {
    	$accountValidator = $this->accountValidator($request->all());
	    if ( $accountValidator->passes() ) {
	        // validation has passed, do validation for dependents
            // save user and dependents in DB
            $userValidator = $this->userValidator($request->all());
	        if ( $userValidator->passes() ) {
                try 
                {
                    DB::beginTransaction();
                    $this->createAccount($request);
                    $this->createUsers($request);
                } catch (\Exception $e) {
                    DB::rollback();
                    return response()->json(['message' => 'db_error', 'errors' => $e.getErrors()], 500);
                }
                DB::commit();
    	        return response()->json(['message' => 'account_created']);
            } else {
                return response()->json(['message' => 'validation_failed', 'errors' => $userValidator->errors()], 422);
            }
	    } else {
	        // validation has failed, display error messages
	        return response()->json(['message' => 'validation_failed', 'errors' => $accountValidator->errors()], 422);
	    }
    }

    public function checkEmail(Request $request) {
    	$account = Account::where('email', '=', $request->only('email'))->first();
		if ($account === null)
			return response()->json(['taken' => false]);
		else
			return response()->json(['taken' => true]);
    }
}
