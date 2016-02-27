<?php

namespace App\Http\Controllers;

use App\User;
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
    		'firstName' => 'required|max:255',
            'lastName' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|confirmed|min:6|alpha_num',
        ]);

    	return $validator;
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    protected function create(Request $request)
    {

    	
        // $user = new User();
        // $user->firstName = $request->firstName;
        // $user->lastName = $request->lastName;
        // $user->email = $request->email;
        // $user->password = bcrypt($request->password);

		// return $user->save();
    }

    public function register(Request $request) {
    	$validator = $this->validator($request->all());
	    if ( $validator->passes() ) {
	        // validation has passed, save user in DB
	        $user = $this->create($request);
	        return response()->json(['message' => 'account_created']);
	    } else {
	        // validation has failed, display error messages
	        return response()->json(['message' => 'validation_failed', 'errors' => $validator->errors()], 400);
	    }
    }
}
