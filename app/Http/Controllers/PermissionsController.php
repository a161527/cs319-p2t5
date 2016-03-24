<?php

namespace App\Http\Controllers;

use Entrust;
use DB;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Models\Account;
use App\Models\Role;

use App\Utility\PermissionNames;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

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

    public function listAccountRoles($email) {
        if(!Entrust::can(PermissionNames::ManageGlobalPermissions())) {
            return response()->json(["message" => "no_global_permissions_ability"], 403);
        }

        $acc = Account::with("roles")->where("email", $email)->get()->first();
        if(!isset($acc)) {
            return response()->json(["message" => "user_not_found"], 400);
        }

        return $this->roleListJson($acc->roles);
    }

    private function doAccountRoleChange($roleNames, $alterUsing) {
        $roles = Role::whereIn("name", $roleNames)->get();
        if(count($roles) != sizeof($roleNames)) {
            return false;
        }
        $alterUsing($roles);
        return true;
    }

    public function changeAccountRoles(Request $req, $email) {
        if(!Entrust::can(PermissionNames::ManageGlobalPermissions())) {
            return response()->json(["message" => "no_global_permissions_ability"], 403);
        }

        try {
            $this->executeRoleChange($req, $email);
        } catch(BadRequestHttpException $e) {
            return response()->json(["message"=> $e->getMessage()], 400);
        }
    }

    private function executeRoleChange(Request $req, $email) {
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
                $okay = $this->doAccountRoleChange($add, function($roles) use ($acc) {$acc->attachRoles($roles);});
            }
            if ($req->has("remove")) {
                $remove = $req->all()["remove"];
                $okay = $this->doAccountRoleChange($remove, function($roles) use ($acc) {$acc->detachRoles($roles);});
            }
            if (isset($okay) && !$okay) {
                throw new BadRequestHttpException("role_not_found");
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
