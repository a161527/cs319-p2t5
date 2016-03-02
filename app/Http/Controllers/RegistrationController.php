<?php

namespace App\Http\Controllers;

use App\Models\Account;
use Validator;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;

use Illuminate\Http\Request;
use Illuminate\Contracts\Logging\Log;

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

    }

    /**
     * Get a validator for an incoming registration request.
     */
    protected function validator(array $data)
    {
    	$validator = Validator::make($data, [
            'email' => 'required|email|max:255|unique:accounts',
            'password' => 'required|confirmed|min:6|alpha_num',
        ]);

    	return $validator;
    }

    /**
     * Create a new user instance after a valid registration.
     */
    protected function create(Request $request)
    {
        $account = new Account();
        $account->email = $request->email;
        $account->password = Hash::make($request->password);
		$account->save();
    }

    public function register(Request $request) {
    	$validator = $this->validator($request->all());
	    if ( $validator->passes() ) {
	        // validation has passed, save user in DB
	        $result = $this->create($request);
	        return response()->json(['message' => 'account_created']);
	    } else {
	        // validation has failed, display error messages
	        return response()->json(['message' => 'validation_failed', 'errors' => $validator->errors()], 422);
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
