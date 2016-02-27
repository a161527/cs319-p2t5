<?php

/*
|--------------------------------------------------------------------------
| Routes File
|--------------------------------------------------------------------------
|
| Here is where you will register all of the routes in an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', function()
{
	// change login.html to whatever the index page for angular will be
    return File::get(public_path() . '/login.html');
});

Route::get('/test/hi', function() {
	return "Hello world";
});

// Route::get('/', function () {
//     return view('welcome');
// });


/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| This route group applies the "web" middleware group to every route
| it contains. The "web" middleware group is defined in your HTTP
| kernel and includes session state, CSRF protection, and more.
|
*/

Route::group(['middleware' => ['web']], function () {
    //
});

Route::group(['prefix' => 'api'], function()
{
	// test for a page that requires a token to be submitted
    Route::resource('login', 'AuthenticationController', ['only' => ['index']]);

    Route::post('login', 'AuthenticationController@authenticate');
    Route::post('register', array('as'=>'register', 'uses'=>'RegistrationController@register'));
});


Route::get('/api/v1/event/{id?}', 'Events@index');
Route::post('/api/v1/event', 'Events@store');
Route::post('/api/v1/event/{id}', 'Events@update');
Route::delete('/api/v1/event/{id}', 'Events@destroy');

