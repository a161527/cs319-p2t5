<?php

namespace App\Http\Controllers;

use Entrust;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Models\Account;

use App\Utility\PermissionNames;

class PermissionsController extends Controller
{
    public function __construct() {
        $this->middleware('jwt.auth');
    }

    private function validateUserRoleListRequest($req) {
        $this->validate($req,
            [
                "user" => "email|required"
            ]
        );
    }

    public function listUserRoles($email) {
        if(!Entrust::can(PermissionNames::ManageGlobalPermissions())) {
            return response()->json(["message" => "no_global_permissions_ability"], 403);
        }

        $acc = Account::with("roles")->where("email", $email)->get()->first();
        if(!isset($acc)) {
            return response()->json(["message" => "user_not_found"], 400);
        }


        $roleJson = [];
        foreach ($acc->roles as $r) {
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
}
