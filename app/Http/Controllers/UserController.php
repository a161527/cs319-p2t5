<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\User;
use Validator;
use DB;

class UserController extends Controller
{
 	public function __construct() 
 	{
 		// $this->middleware('jwt.auth', ['except' => ['authenticate', 'token']]);
        // provides an authorization header with each response
        // $this->middleware('jwt.refresh', ['except' => ['authenticate', 'token']]);
 	}
	
    /*
     * Get a validator for an incoming registration request.
     */
    protected function dependentValidator(array $data)
    {
    	$validator = Validator::make($data, [
            // need to change the name fields to allow accented characters, dashes, apostrophes, etc.
    		'firstName'	=>	'required|max:255|alpha_num',
    		'lastName'	=>	'required|max:255|alpha_num',
    		'dateOfBirth'	=>	'date_format:Y-m-d',
            'gender'	=>	'in:male,female'
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
    protected function insertDependent($accountId, $data)
    {
    	$user = new User();
    	$user->firstName = $data['firstName'];
    	$user->lastName = $data['lastName'];
    	$user->dateOfBirth = $data['dateOfBirth'];
    	$user->gender = $data['gender'];
    	$user->accountId = $accountId;
    	$user->save();
    }

    //
    // TODO: isOwner() and isAdmin(), to set permission/role filters on managing dependents
    //

	/* 
	 * GET api/accounts/{id}/dependents
	 * - list all dependents
	 */
 	public function index($accountId)
    {
    	// TODO: add permission/role filter
        //  - get id from submitted token
    	$account = Account::where('id', '=', $accountId)->first();
		if ($account === null)
			return response()->json(['message' => 'account_not_found']);
		else
		{
			$dependents = User::where('accountID', '=', $account->id)->get();
			return response()->json(['message' => 'returned_dependents', 'dependents' => $dependents]);
		}
    }

    /*
     * POST api/accounts/{accountId}/dependents
     * PUT api/accounts/{accountId}/dependents
     * - create new dependent(s)
     */
    public function addDependents($accountId, Request $req)
    {
    	$dependents = $req->all();
        try
        {   
            DB::beginTransaction();
        	foreach ($dependents as $d)
        	{
    			$validator = $this->dependentValidator($d);
    	    	if ($validator->passes())
    	    		$this->insertDependent($accountId, $d);
    	    	else
    	    		return response()->json(['message' => 'validation_failed', 'errors' => $validator->errors()], 422);
        	}
            DB::commit();
            return response()->json(['message' => 'dependents_added']);
        } catch (ValidationException $e) {
            DB::rollback();
            return reponse()->json(['message' => 'user_could_not_be_added', 'errors' => $e.getErrors()], 500);
        } catch (\Exception $e) {
            DB::rollback();
            return reponse()->json(['message' => 'unknown_error', 'errors' => $e.getErrors()], 500);
        }
    }

    /*
     * PATCH api/accounts/{id}/dependents/{depId}
     * - updates the provided values for dependent with id={depId}
     */
    public function editDependent($accountId, $depId, Request $req)
    {
        $changes = $req->all();
        // dd($changes);
        $user = User::where('id', $depId)
                    ->where('accountId', $accountId)
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
    public function deleteDependent($accountId, $depId)
    {
        $user = User::where('accountId', '=', $accountId)
                    ->where('id', '=', $depId);
        if ($user->delete())
            return response()->json(['message' => 'user_deleted'], 200);
        else
            return response()->json(['message' => 'user_could_not_be_deleted'], 500);
    }
}
