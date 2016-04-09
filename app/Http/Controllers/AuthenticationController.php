<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Models\Account;
use App\Models\Permission;
use App\Jobs\ResetPassword;
use Auth;
use Entrust;
use App\Utility\PermissionNames;
use Log;
use Hash;

class AuthenticationController extends Controller
{

    public function __construct()
    {
        // Apply the jwt.auth.rejection middleware to all methods in this controller
        // except for the authenticate method. We don't want to prevent
        // the user from retrieving their token if they don't already have it

        $this->middleware('jwt.auth.rejection', ['except' => ['authenticate', 'token', 'resetPassword']]);
    }

    public function index(Request $request)
    {
        $permissions = $this->buildPermissionsJson();
        $accountID = Auth::user()->id;

        return response()->json(['message' => 'successful_login', 'token' => JWTAuth::fromUser(Auth::user()), 'permissions' => $permissions, 'accountID' => $accountID, 'email' => Auth::user()->email]);
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
        $accountID = Auth::user()->id;

        // if no errors are encountered we can return a JWT
        return response()->json(['message' => 'successful_login', 'token' => $token, 'permissions' => $permissions, 'accountID' => $accountID, 'email' => Auth::user()->email]);
    }

    public function editAccount(Request $req) {
        $user = Auth::user();
        if ($req->has('password')) {
            $user->password = Hash::make($req->input('password'));
        }

        if ($req->has('receiveUpdates')) {
            $user->receiveUpdates = $req->input('receiveUpdates') ? true : false;
        }
        $user->save();
        return response()->json(['message' => 'account_updated']);
    }

    public function receiveUpdates() {
        return response()->json(['receiveUpdates' => Auth::user()->receiveUpdates]);
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

    public function resetPassword(Request $request) {
        if (!$request->has('email')) {
            return response()->json(['message' => 'no_email_given'], 400);
        }

        $account = Account::where('email', $request->input('email'))->get()->first();
        if (!isset($account)) {
            return response()->json(['message' => 'account_not_found'], 400);
        }

        $this->dispatch(new ResetPassword($account));

        return ["message" => "email_pending"];
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
