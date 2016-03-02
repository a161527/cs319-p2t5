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

Route::group(['prefix' => 'api/conferences'], function () {
    Route::get('', 'ConferenceController@getInfoList');
    Route::post('', 'ConferenceController@createNew');

    Route::group(['prefix' => '{confId}'], function () {
        Route::get('', 'ConferenceController@getInfo');
        Route::put('', 'ConferenceController@replace');
        Route::delete('', 'ConferenceController@delete');
    });
});

Route::get('/', function()
{
	// change login.html to whatever the index page for angular will be
    return File::get(public_path() . '/login.html');
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
    Route::post('register', 'RegistrationController@register');

    // check if email is taken
    Route::post('checkemail', 'RegistrationController@checkEmail');

    // refresh token
    Route::get('token', 'AuthenticationController@token');

    // dependents management endpoints
    Route::group(['prefix' => 'accounts/{id}/dependents'], function() {
        Route::get('/', 'UserController@index');
        
    });
});

// Routes for Event
Route::get('/api/v1/event/{id?}', 'Events@index');
Route::post('/api/v1/event', 'Events@store');
Route::post('/api/v1/event/{id}', 'Events@update');
Route::delete('/api/v1/event/{id}', 'Events@destroy');

