<?php

namespace App\Http\Controllers;

use Entrust;
use DB;
use Auth;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Models\Account;
use App\Models\Role;
use App\Models\Permission;
use App\Event;

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
        $acc = Account::with("roles")->where("email", $email)->get()->first();
        if(!isset($acc)) {
            return response()->json(["message" => "user_not_found"], 400);
        }

        $accessibleRoleNames = $this->manageableRoleNamesForUser();

        $roles = [];

        foreach ($acc->roles as $r) {
            if (in_array($r->name, $accessibleRoleNames)) {
                $roles[] = $r;
            }
        }

        return $this->roleListJson($roles);
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
        try {
            return $this->executeRoleChange($req, $email);
        } catch(BadRequestHttpException $e) {
            return response()->json(["message"=> $e->getMessage()], 400);
        }
    }

    private function manageableRoleNamesForUser() {
        $roles = $this->manageableRolesForUser()->toArray();

        return array_map(
            function ($r) { return $r['name']; },
            $roles);
    }

    private function roleResultToList($roles) {
        $roleArray = [];
        foreach ($roles as $r) {
            $roleArray[] = $r;
        }

        return $roleArray;
    }


    private function manageableRolesForUser() {
        $roles = Role::with('perms')->get();
        if (Entrust::can(PermissionNames::ManageGlobalPermissions())) {
            return $roles;
        }

        $roles = $this->roleResultToList($roles);

        //Filter out global permissions
        $roles = array_filter(
            $roles,
            function ($r) {
                $globalPerms = PermissionNames::AllGlobalPermissions();
                foreach ($r->perms as $p) {
                    if (in_array($p->name, $globalPerms)) {
                        return false;
                    }
                }
                return true;
            });

        $confPermNamePart =
            PermissionNames::normalizePermissionName(
                PermissionNames::ConferencePermissionManagement(1));

        $evtPermNamePart =
            PermissionNames::normalizePermissionName(
                PermissionNames::EventPermissionsManagement(1));

        //Get the permissions this user has which are permissions management
        //permissions
        $currentPermManagement = Permission::whereHas("roles", function ($query) {
            $query->whereHas("users", function ($query) {
                //on Account table
                $query->where("id", Auth::user()->id);
            });
        })->where('name', 'like', $confPermNamePart . '%')
          ->orWhere('name', 'like', $evtPermNamePart . '%')
          ->get();

        $conferences = [];
        $events = [];

        foreach ($currentPermManagement as $perm) {
            if (PermissionNames::isConferencePermission($perm->name)) {
                $conferences[] = PermissionNames::extractPermissionData($perm->name)->idPart;
            } else {
                $events[] = PermissionNames::extractPermissionData($perm->name)->idPart;
            }
        }

        $ownedEvents = Event::whereIn('conferenceID', $conferences)->select('id')->get();
        $ownedEvents = array_map(function ($e) { return $e['id']; }, $ownedEvents->toArray());
        $events = array_merge($events, $ownedEvents);

        //Filter out permissions not associated with the conferences/events
        //this user can control.
        $roles = array_filter(
            $roles,
            function($r) use ($events, $conferences){
                foreach ($r->perms as $p) {
                    if (PermissionNames::isConferencePermission($p->name)) {
                        $confId = PermissionNames::extractPermissionData($p->name)->idPart;
                        if (!in_array($confId, $conferences)) {
                            return false;
                        }
                    } else if (PermissionNames::isEventPermission($p->name)) {
                        $evtId = PermissionNames::extractPermissionData($p->name)->idPart;
                        if (!in_array($evtId, $events)) {
                            return false;
                        }
                    }
                    return true;
                }
            });
        return $roles;
    }

    private function executeRoleChange(Request $req, $email) {
        return DB::transaction(function () use ($email, $req) {
            $acc = Account::with("roles")->where("email", $email)->get()->first();
            if(!isset($acc)) {
                return response()->json(["message" => "user_not_found"], 400);
            }

            $accessibleRoleNames = $this->manageableRoleNamesForUser();

            if ($req->has("add")) {
                $allAdd = $req->all()["add"];

                foreach ($allAdd as $rname) {
                    if (!in_array($rname, $accessibleRoleNames)) {
                        throw new BadRequestHttpException("role_not_accessible");
                    }
                }

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


                foreach ($remove as $rname) {
                    if (!in_array($rname, $accessibleRoleNames)) {
                        throw new BadRequestHttpException("role_not_accessible");
                    }
                }

                $okay = $this->doAccountRoleChange($remove, function($roles) use ($acc) {$acc->detachRoles($roles);});
            }

            if (isset($okay) && !$okay) {
                throw new BadRequestHttpException("role_not_found");
            }
            return response()->json(["message" => "roles_patched"]);
        });
    }

    public function listAssignableRoles() {
        return $this->roleListJson($this->manageableRolesForUser());
    }
}
