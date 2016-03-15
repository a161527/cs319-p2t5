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

//All conference endpoints
Route::group(['prefix' => 'api/conferences', 'namespace' => 'Conference'], function () {
    Route::get('', 'MainController@getInfoList');
    Route::post('', 'MainController@createNew');

    Route::group(['prefix' => '{confId}'], function () {
        Route::get('', 'MainController@getInfo');
        Route::put('', 'MainController@replace');
        Route::delete('', 'MainController@delete');

        Route::get('permissions', 'ConferenceController@getPermissions');

        //New registration request
        Route::post('register', 'RegistrationController@userRegistration');
        Route::post('register/{registryId}/approve', 'RegistrationController@approveRegistration');
        Route::get('register/{registryId}', 'RegistrationController@getRegistrationData');
    });
});

Route::get('/', function()
{
    // change login.html to whatever the index page for angular will be
    return File::get(public_path() . '/index.html');
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
    // test GET for a page that requires a token to be submitted
    Route::resource('login', 'AuthenticationController', ['only' => ['index']]);

    Route::get('permissions', 'AuthenticationController@permissionList');

    Route::post('login', 'AuthenticationController@authenticate');
    Route::post('register', 'RegistrationController@register');

    // check if email is taken
    Route::get('checkemail', 'RegistrationController@checkEmail');
    Route::post('checkemail', 'RegistrationController@checkEmail');

    // refresh token
    Route::get('token', 'AuthenticationController@token');

    // dependents management endpoints
    Route::group(['prefix' => 'accounts/{id}/dependents'], function() {
        Route::get('/', 'UserController@index');
        Route::post('/', 'UserController@addDependents');
        Route::put('/', 'UserController@addDependents');
        Route::patch('/{depId}', 'UserController@editDependent');
        Route::delete('/{depId}', 'UserController@deleteDependent');
    });
});

// Routes for Event
Route::get('/api/event/{id?}', 'Events@index');
Route::get('/api/event/conference/{id?}', 'Events@getEventByConferenceID');
Route::post('/api/event/{id}', 'Events@store');
Route::post('/api/event/{id}/update', 'Events@update');
Route::delete('/api/event/{id}', 'Events@destroy');
