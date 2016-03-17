<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Models\User;

class UsersTest extends TestCase
{
	use WithoutMiddleware;
	use DatabaseTransactions;

    public function signIn($data=['email'=>'ryanchenkie@gmail.com', 'password'=>'secret'])
	{
	    $this->post('api/login', $data);
	    $content = json_decode($this->response->getContent());

	    $this->assertObjectHasAttribute('token', $content, 'Token does not exist');
	    $this->token = $content->token;

	    return $this;
	}

    public function testGetDependents()
    {
    	$this->get('api/accounts/1/dependents')
			->seeJson([
				'message' => 'returned_dependents'
			]);
    }

    public function testAddDependent()
    {
    	$this->signIn();
    	$data = [
    	array(
    		'token' => $this->token,
    		'firstName' => 'test',
    		'lastName' => 'dependent',
    		'dateOfBirth' => '1948-01-01',
    		'gender' => 'male'
    	)];
    	$this->json('POST', 'api/accounts/1/dependents', $data)
			->seeJson([
				'message' => 'dependents_added'
		 	]);

    	$this->seeInDatabase('users', ['firstName'=>'test', 'lastName'=>'dependent']);
    }

    // TODO: below

    // public function testEditDependent()
    // {
    // 	// do update dependent
    // 	$data = ['firstName'=>'Chris', 'lastName'=>'Somelastname'];
    // 	$user = User::where($data)->first();
    // 	$user->firstName = "ChrisP";
    // 	$user->save();
    	
    // 	// assert see updated entry in db
    // 	$this->seeInDatabase('users', ['firstName'=>'ChrisP', 'lastName'=>'Somelastname']);
    // }

    // public function testDeleteDependent()
    // {
    // 	$data = ['firstName'=>'Chris', 'lastName'=>'Somelastname'];
    // 	$notdata = ['firstName'=>'Chris', 'lastName'=>'Somelastname', 'deleted_at'=>null];
    // 	// do delete dependent
    // 	$user = User::where($data)->delete();

    // 	// assert check deleted dependent
    // 	$this->notSeeInDatabase('users', $notdata);
    // }
}
