<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\User;
use Validator;

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
    		'firstName'	=>	'required|max:255|regex:#[^\p{L}\s-]#u',
    		'lastName'	=>	'required|max:255',
    		'dateOfBirth'	=>	'date_format:Y-m-d',
            'gender'	=>	'in:male,female'
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

	/* 
	 * GET api/accounts/{id}/dependents
	 * - list all dependents
	 */
 	public function index($accountId)
    {
    	//TODO: add permission/role filter
    	$account = Account::where('id', '=', $accountId)->first();
		if ($account === null)
			return response()->json(['message' => 'account_not_found']);
		else
		{
			$dependents = User::where('accountID', '=', $account->id)->get();
			return response()->json(['dependents' => $dependents]);
		}
    }

    /*
     * POST api/accounts/{id}/dependents
     * PUT api/accounts/{id}/dependents
     * - create new dependent(s)
     */
    public function addDependents($accountId, Request $req)
    {
    	$dependents = $req->all();
    	// TODO: loop through dependents array in $req
    	foreach ($dependents as $d)
    	{
			$validator = $this->dependentValidator($d);
	    	if ($validator->passes())
	    		$this->insertDependent($accountId, $d);
	    	else
	    		return response()->json(['message' => 'validation_failed', 'errors' => $validator->errors()], 422);
	    	// $user = new User()
    	}
	    
    	dd("addDependent", $dependents);
    }

    /*
     * PATCH api/accounts/{id}/dependents/{depId}
     * - updates the provided values for dependent with id={depId}
     */
    public function editDependent($accountId, $depId, Request $req)
    {
    	dd("editDependent: accountId={$accountId}, depId={$depId}. ", $req->all());	
    }

    /*
     * DELETE api/accounts/{id}/dependents/{dep_id}
     * - deletes the corresponding dependent
     */
    public function deleteDependent($accountId, $depId)
    {
    	dd("deleteDependent", $req->all());
    }
}
