<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Models\Account;
use App\Models\Permission;
use Auth;
use Entrust;
use App\Utility\PermissionNames;


class AuthenticationController extends Controller
{

    public function __construct()
    {
        // Apply the jwt.auth.rejection middleware to all methods in this controller
        // except for the authenticate method. We don't want to prevent
        // the user from retrieving their token if they don't already have it

        $this->middleware('jwt.auth.rejection', ['except' => ['authenticate', 'token']]);
        // provides an authorization header with each response
        $this->middleware('jwt.refresh', ['except' => ['authenticate', 'token']]);

    }

    public function index(Request $request)
    {
        // token must be submitted with request in order for this to no throw error "token_not_provided"

        // returns the logged-in user
        // must call JWTAuth::authenticate() and then you can use Laravel's Auth::user()->id
        // source: https://github.com/tymondesigns/jwt-auth/issues/125

        $account = JWTAuth::parseToken()->authenticate();
        $accountID = $account->id;

        return $account;
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

        $permissions = $this->buildPermissionsJson();
        $accountID = Account::where('email', '=', $credentials['email'])->select('id')->first()['id'];

        // if no errors are encountered we can return a JWT
        return response()->json(['message' => 'successful_login', 'token' => $token, 'permissions' => $permissions, 'accountID' => $accountID]);
    }

    public function token()
    {
        $token = JWTAuth::getToken();
        if (!$token)
        {
            return response()->json(['message' => 'token_not_provided'], 401);
        }
        try
        {
            $token = JWTAuth::refresh($token);
        }
        catch (TokenInvalidException $e)
        {
            return response()->json(['message' => 'token_invalid'], 401);
        }
        return response()->json(['token'=>$token]);
    }

    // Add the token to the blacklist
    public function logout()
    {
        //
        $token = JWTAuth::getToken();
        if ($token) {
            JWTAuth::setToken($token)->invalidate();
        }
    }

    public function permissionList(){
        return response()->json($this->buildPermissionsJson());
    }

    private function buildPermissionsJson() {
        $permissions = [];
        $this->checkPermission(PermissionNames::CreateConference(), $permissions);
        $this->checkPermission(PermissionNames::ManageGlobalPermissions(), $permissions);
        $this->checkPermission(PermissionNames::ApproveUserRegistration(), $permissions);
        $this->checkPermission(PermissionNames::ViewSiteStatistics(), $permissions);

        if (!is_null(Auth::user())) {
            $pnames = Permission::whereHas("roles", function ($query) {
                $query->whereHas("users", function ($query) {
                    $query->where("id", Auth::user()->id);
                });
            })->select("name")->get()->toArray();

            $pnames = array_map(function($p) {return $p['name'];}, $pnames);
            $lookFor = PermissionNames::permissionManagementPermissionBases();
            foreach($pnames as $permName) {
                $normal = PermissionNames::normalizePermissionName($permName);
                if (in_array($normal, $lookFor)) {
                    $permissions[] = "manage-some-permissions";
                    break;
                }
            }
        }

        return $permissions;
    }

    private function checkPermission($permName, &$permissionsArray) {
        if(Entrust::can($permName)) {
            $permissionsArray[] = $permName;
        }
    }
}
