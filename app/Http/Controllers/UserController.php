<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Account;
use App\User;
use Validator;
use DB;
use Entrust;
use App\Utility\PermissionNames;
use Auth;

class UserController extends Controller
{
     public function __construct()
     {
        $this->middleware('jwt.auth.rejection');
     }

    /*
     * Get a validator for an incoming registration request.
     */
    protected function dependentValidator(array $data)
    {
        $validator = Validator::make($data, [
            // need to change the name fields to allow accented characters, dashes, apostrophes, etc.
            'firstName'    =>    'required|max:255|alpha_num',
            'lastName'    =>    'required|max:255|alpha_num',
            'dateOfBirth'    =>    'date_format:Y-m-d',
            'gender'    =>    'in:male,female'
        ]);

        return $validator;
    }

    /*
     * Get a validator for an incoming registration request.
     */
    protected function dependentEditValidator(array $data)
    {
        $validator = Validator::make($data, [
            // need to change the name fields to allow accented characters, dashes, apostrophes, etc.
            'firstName' =>  'max:255|alpha_num',
            'lastName'  =>  'max:255|alpha_num',
            'dateOfBirth'   =>  'date_format:Y-m-d',
            'gender'    =>  'in:male,female'
        ]);

        return $validator;
    }

    /*
     * Insert a validated user into the database
     */
    protected function insertDependent($accountID, $data)
    {
        $user = new User();
        $user->firstName = $data['firstName'];
        $user->lastName = $data['lastName'];
        $user->dateOfBirth = $data['dateOfBirth'];
        $user->gender = $data['gender'];
        $user->accountID = $accountID;
        $user->save();
    }

    //
    // TODO: isOwner() and isAdmin(), to set permission/role filters on managing dependents
    //

    /*
     * GET api/accounts/{id}/dependents
     * - list all dependents
     */
     public function index($accountID)
    {
        // TODO: add permission/role filter
        $account = Account::where('id', '=', $accountID)->first();
        if ($account === null)
            return response()->json(['message' => 'account_not_found']);
        else
        {
            $dependents = User::where('accountID', '=', $account->id)->get();
            return response()->json(['message' => 'returned_dependents', 'dependents' => $dependents]);
        }
    }

    /*
     * POST api/accounts/{accountID}/dependents
     * PUT api/accounts/{accountID}/dependents
     * - create new dependent(s)
     */
    public function addDependents($accountID, Request $req)
    {
        $dependents = $req->all();
        try
        {
            DB::beginTransaction();
            foreach ($dependents as $d)
            {
                $validator = $this->dependentValidator($d);
                if ($validator->passes())
                    $this->insertDependent($accountID, $d);
                else
                    return response()->json(['message' => 'validation_failed', 'errors' => $validator->errors()], 422);
            }
            DB::commit();
            return response()->json(['message' => 'dependents_added']);
        } catch (ValidationException $e) {
            DB::rollback();
            return response()->json(['message' => 'user_could_not_be_added', 'errors' => $e->getMessage()], 500);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['message' => 'unknown_error', 'errors' => $e->getMessage()], 500);
        }
    }

    /*
     * PATCH api/accounts/{id}/dependents/{depId}
     * - updates the provided values for dependent with id={depId}
     */
    public function editDependent($accountID, $depId, Request $req)
    {
        $changes = $req->all();
        // dd($changes);
        $user = User::where('id', $depId)
                    ->where('accountID', $accountID)
                    ->first();
        if (!$user)
            return response()->json(['message' => 'user_does_not_exist'], 422);
        else
        {
            $validator = $this->dependentEditValidator($changes);
            if ($validator->passes())
                // do updates
                foreach ($changes as $field => $newValue)
                {
                    switch($field)
                    {
                        case "firstName":
                            $user->firstName = $newValue;
                            break;
                        case "lastName":
                            $user->lastName = $newValue;
                            break;
                        case "dateOfBirth":
                            $user->dateOfBirth = $newValue;
                            break;
                        case "gender":
                            $user->gender = $newValue;
                            break;
                    }
                }
            else
                return response()->json(['message' => 'validation_failed', 'errors' => $validator->errors()], 422);
        }

        if ($user->save())
            return response()->json(['message' => 'user_updated'], 200);
        else
            return response()->json(['message' => 'user_could_not_be_updated'], 500);
    }

    /*
     * DELETE api/accounts/{id}/dependents/{dep_id}
     * - deletes the corresponding dependent
     */
    public function deleteDependent($accountID, $depId)
    {
        $user = User::where('accountID', '=', $accountID)
                    ->where('id', '=', $depId);
        if ($user->delete())
            return response()->json(['message' => 'user_deleted'], 200);
        else
            return response()->json(['message' => 'user_could_not_be_deleted'], 500);
    }

    /*
     * GET api/accounts/{id}/dependents/approved
     * - list of approved dependents
     */
    public function approvedDependents($accountID)
    {
        if (!Entrust::can(PermissionNames::ApproveUserRegistration()) && Auth::user()->id != $accountID) {
            return response()->json(["message" => "no_user_approval_access"]);
        }

        $account = Account::where('id', '=', $accountID)->first();
        if ($account === null)
            return response()->json(['message' => 'account_not_found']);
        else
        {
            $dependents = User::where('accountID', '=', $account->id)
                              ->where('approved', '=', true)
                              ->get();
            return response()->json(['message' => 'returned_approved_dependents', 'dependents' => $dependents]);
        }
    }

    /*
     * GET api/dependents/approved
     * - returns all approved dependents
     */
    public function allApproved()
    {
        if (!Entrust::can(PermissionNames::ApproveUserRegistration()) && Auth::user()->id != $accountID) {
            return response()->json(["message" => "no_user_approval_access"]);
        }

        $dependents = User::where('approved', 1)->get();
        return response()->json(['message' => 'returned_approved_dependents', 'dependents' => $dependents]);
    }

    /*
     * GET api/dependents/unapproved
     * - returns all unapproved dependents
     */
    public function allUnapproved()
    {
        if (!Entrust::can(PermissionNames::ApproveUserRegistration()) && Auth::user()->id != $accountID) {
            return response()->json(["message" => "no_user_approval_access"]);
        }

        $dependents = User::where('approved', 0)->get();
        return response()->json(['message' => 'returned_unapproved_dependents', 'dependents' => $dependents]);
    }
}
