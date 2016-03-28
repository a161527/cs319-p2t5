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

        Route::get('permissions', 'MainController@getPermissions');

        //New registration request
        Route::post('register', 'RegistrationController@userRegistration');
        Route::post('register/{registryId}/approve', 'RegistrationController@approveRegistration');
        Route::delete('register/{registryId}', 'RegistrationController@removeRequest');
        Route::get('register/{registryId}', 'RegistrationController@getRegistrationData');
        Route::get('register', 'RegistrationController@outstandingRegistrationRequests');

        Route::group(['prefix' => 'residences'], function () {
            Route::get('', 'RoomSetupController@getResidenceList');
            Route::post('upload', 'RoomSetupController@uploadRoomData');
            Route::post('', 'RoomSetupController@createResidence');

            Route::group(['prefix' => '{residenceId}'], function () {
                Route::get('', 'RoomSetupController@getResidenceInfo');
                Route::patch('', 'RoomSetupController@editResidence');
                Route::get('roomSets', 'RoomSetupController@getResidenceRoomSets');
                Route::get('roomTypes', 'RoomSetupController@getResidenceRoomTypes');
                Route::post('roomSets', 'RoomSetupController@createRoomSet');

                Route::get('rooms/{roomName}/users', 'RoomAssignmentController@getRoomUsers');
            });


            Route::get('roomSets/{setId}/rooms', 'RoomAssignmentController@roomsInSet');
            Route::get('roomSets/{setId}', 'RoomSetupController@getRoomSetInfo');
            Route::patch('roomSets/{setId}', 'RoomSetupController@editRoomSet');

            Route::post('assign', 'RoomAssignmentController@assignRoom');
            Route::delete('assign/{assignId}', 'RoomAssignmentController@deleteAssignment');
            Route::get('assign', 'RoomAssignmentController@listAssignments');
            Route::get('assign/missing', 'RoomAssignmentController@missingAssignments');
        });

    });
});

Route::group(['prefix' => '/api/roles'], function() {
    Route::get('/account/{accountMail}', 'PermissionsController@listAccountRoles');
    Route::patch('/account/{accountMail}', 'PermissionsController@changeAccountRoles');
    Route::get('/assignable', 'PermissionsController@listAssignableRoles');
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
    Route::delete('account/{email}', 'RegistrationController@deleteAccount');

    Route::get('unapprovedUsers', 'RegistrationController@listUnapproved');
    Route::post('register/{id}/approve', 'RegistrationController@approveUser');

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
        Route::get('/approved', 'UserController@approvedDependents');
    });
    Route::get('dependents/approved', 'UserController@allApproved');
    Route::get('dependents/unapproved', 'UserController@allUnapproved');

    // inventory management
    Route::group(['prefix' => 'conferences/{confId}/inventory'], function() {
        Route::get('/', 'InventoryController@index');
        Route::get('/unapproved', 'InventoryController@unapproved');
        Route::get('/approved', 'InventoryController@approved');
        Route::post('/', 'InventoryController@addItem');
        Route::post('/reserve', 'InventoryController@reserveItem');

        Route::patch('/{itemId}', 'InventoryController@editItem');
        Route::delete('/{itemId}', 'InventoryController@deleteItem');
        Route::get('/{itemId}',  'InventoryController@getItem');
    });

    // user_inventory
    Route::group(['prefix' => 'userinventory/{id}'], function() {
        Route::get('/approve', 'InventoryController@approveRequest');
    });

    // transportation
    Route::group(['prefix' => 'conferences/{confId}/transportation'], function() {
        Route::get('/{id}', 'TransportationController@getTransport');
        Route::post('/', 'TransportationController@addTransport');
        Route::delete('/{id}', 'TransportationController@deleteTransport');
        Route::patch('{id}', 'TransportationController@patchTransport');
        // list all transports
        Route::get('/', 'TransportationController@index');
        // assign/unassign
        Route::post('/{id}/assign', 'TransportationController@assignTransport');
        Route::post('/{id}/unassign', 'TransportationController@unassignTransport');
        // view users needing transport (conf id, time)
        // Route::get('summary/', 'TransportationController@transportSummary');
    });
    Route::get('conferences/{confId}/transportationsummary', 'TransportationController@transportSummary');

});

// Routes for Event
Route::get('/api/event/{id?}', 'Events@index');
Route::get('/api/event/conference/{id?}', 'Events@getEventByConferenceID');
Route::post('/api/event/{confId}', 'Events@store');
Route::put('/api/event/{id}', 'Events@update');
Route::post('/api/event/{id}/register', 'Events@register');
Route::delete('/api/event/{id}', 'Events@destroy');
