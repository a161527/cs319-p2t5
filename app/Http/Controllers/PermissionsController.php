<?php

namespace App\Http\Controllers;

use Entrust;
use DB;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Models\Account;
use App\Models\Role;

use App\Utility\PermissionNames;

class PermissionsController extends Controller
{
    public function __construct() {
        $this->middleware('jwt.auth');
    }

    private function roleListJson($roles) {
        $roleJson = [];
        foreach ($roles as $r) {
            $permData = PermissionNames::extractPermissionData($r->name);
            if (isset($r->displayName)) {
                $displayName = $r->displayName;
            } else {
                $wordified = str_replace("-", " ", $permData->namePart);
                $displayName = ucwords($wordified);
            }
            $retData = ["name" => $r->name, "displayName" => $displayName];
            if (isset($permData->idPart)) {
                $retData["forId"] = $permData->idPart;
            }
            $roleJson[] = $retData;
        }

        return $roleJson;
    }

    public function listUserRoles($email) {
        if(!Entrust::can(PermissionNames::ManageGlobalPermissions())) {
            return response()->json(["message" => "no_global_permissions_ability"], 403);
        }

        $acc = Account::with("roles")->where("email", $email)->get()->first();
        if(!isset($acc)) {
            return response()->json(["message" => "user_not_found"], 400);
        }

        return $this->roleListJson($acc->roles);
    }

    private function doUserRoleChange($roleNames, $alterUsing) {
        $roles = Role::whereIn("name", $roleNames)->get();
        if(count($roles) != sizeof($roleNames)) {
            return response()->json(["message" => "role_not_found"], 400);
        }
        $alterUsing($roles);
    }

    public function changeUserPermissions(Request $req, $email) {
        if(!Entrust::can(PermissionNames::ManageGlobalPermissions())) {
            return response()->json(["message" => "no_global_permissions_ability"], 403);
        }

        return DB::transaction(function () use ($email, $req) {
            $acc = Account::with("roles")->where("email", $email)->get()->first();
            if(!isset($acc)) {
                return response()->json(["message" => "user_not_found"], 400);
            }


            if ($req->has("add")) {
                $allAdd = $req->all()["add"];

                //Need to be careful not to add roles that are already in
                //as this causes primary key violations.
                $accRoleNames = array_map(
                    function ($role) {
                        return $role['name'];
                    },
                    $acc->roles->toArray());

                $add =
                    array_filter(
                        $allAdd,
                        function($name) use ($accRoleNames) {
                            return !in_array($name, $accRoleNames);
                        });
                $this->doUserRoleChange($add, function($roles) use ($acc) {$acc->attachRoles($roles);});
            }
            if ($req->has("remove")) {
                $remove = $req->all()["remove"];
                $this->doUserRoleChange($remove, function($roles) use ($acc) {$acc->detachRoles($roles);});
            }
            return response()->json(["message" => "roles_patched"]);
        });
    }

    public function listAssignableRoles() {
        if(!Entrust::can(PermissionNames::ManageGlobalPermissions())) {
            return response()->json(["message" => "no_global_permissions_ability"], 403);
        }

        return $this->roleListJson(Role::all());
    }
}
