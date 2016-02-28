<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\User;


class AuthenticationController extends Controller
{

    public function __construct()
    {
        // Apply the jwt.auth middleware to all methods in this controller
        // except for the authenticate method. We don't want to prevent
        // the user from retrieving their token if they don't already have it

        // TODO: add jwt.refresh to middlewares to provide a new token to the response header
        $this->middleware('jwt.auth', ['except' => ['authenticate']]);
    }

    public function index()
    {
        // token must be submitted with request in order for this to no throw error "token_not_provided"
        
        // returns the logged-in user
        // must call JWTAuth::authenticate() and then you can use Laravel's Auth::user()->id
        // source: https://github.com/tymondesigns/jwt-auth/issues/125
        $user = JWTAuth::parseToken()->authenticate();
        $userId = $user->id;

        return $user;
    }    
  
    public function authenticate(Request $request)
    {
        $credentials = $request->only('email', 'password');

        try {
            // verify the credentials and create a token for the user
            if (! $token = JWTAuth::attempt($credentials)) {
                return response()->json(['message' => 'invalid_credentials'], 401);
            }
        } catch (JWTException $e) {
            // something went wrong
            return response()->json(['message' => 'could_not_create_token'], 500);
        }

        // if no errors are encountered we can return a JWT
        return response()->json(compact('token'));
    }

    // Add the token to the blacklist
    public function logout() {
        //
        $token = JWTAuth::getToken();
        if ($token) {
            JWTAuth::setToken($token)->invalidate();
        }
    }
}