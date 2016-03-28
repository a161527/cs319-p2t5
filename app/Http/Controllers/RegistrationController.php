<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\User;
use Validator;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Contracts\Logging\Log;
use DB;
use Hash;
use Entrust;
use Auth;
use App\Utility\PermissionNames;

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
        $this->middleware('jwt.auth', ["only" => ["listUnapproved", "approveUser"]]);
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
        $users = $data["dependents"];
        $validator = Validator::make($users, [
            '*.firstName' => 'required',
            '*.lastName' => 'required',
            '*.dateOfBirth' => 'required',
            '*.gender' => 'required|in:male,female,Male,Female,M,F'
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
        return $account->id;
    }

    /**
     * Create a new user instance after a valid registration.
     */
    protected function createUsers(Request $request, $id)
    {
        $users = $request->all()["dependents"];
        foreach ($users as $u)
        {
            $user = new User();
            $user->firstName = $u["firstName"];
            $user->lastName = $u["lastName"];
            $user->gender = $u["gender"];
            $user->dateOfBirth = $u["dateOfBirth"];
            $user->accountID = $id;
            $user->approved = 0;
            if (isset($u["location"]))
                $user->location = $u["location"];
            if (isset($u["notes"]))
                $user->location = $u["notes"];
            $user->save();
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
                    $id = $this->createAccount($request);
                    $this->createUsers($request, $id);
                } catch (\Exception $e) {
                    DB::rollback();
                    return response()->json(['message' => 'insert_error', 'errors' => $e->getMessage()], 500);
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

    public function deleteAccount($email) {
        $account = Account::where('email', $email)->get()->first();

        if (!isset($account)) {
            return response()->json(['message' => 'account_deleted']);
        }

        if (!Entrust::can(PermissionNames::ManageAccounts()) && $account->id != Auth::user()->id) {
            return response()->json(['message' => 'cannot_manage_account'], 403);
        }

        $account->delete();
        return ["message" => "account_deleted"];
    }

    public function checkEmail(Request $request) {
        $account = Account::where('email', '=', $request->only('email'))->first();
        if ($account === null)
            return response()->json(['taken' => false]);
        else
            return response()->json(['taken' => true]);
    }

    public function listUnapproved() {
        if (!Entrust::can(PermissionNames::ApproveUserRegistration())) {
            return response()->json(["message" => "cannot_approve_users"], 403);
        }

        return User::where('approved', false)->with("account")->get();
    }

    public function approveUser($id) {
        if (!Entrust::can(PermissionNames::ApproveUserRegistration())) {
            return response()->json(["message" => "cannot_approve_users"], 403);
        }

        $user = User::find($id);
        if(!isset($user)) {
            return response()->json(["message" => "user_does_not_exist", 404]);
        }

        $user->approved = 1;
        $user->save();

        //return 200 OKAY
        return "";
    }
}
