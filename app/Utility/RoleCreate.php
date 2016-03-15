<?php

namespace App\Utility;

use App\Models\Role;
use App\Models\Permission;

use DB;

/**
 * Utility for creating new roles for conferences/events.  Also
 * handles permission creation for those roles.
 */
class RoleCreate {
    private static function createPermission($name) {
        $permission = new Permission;
        $permission->name = $name;
        $permission->save();
        return $permission;
    }
    
    //Creates a permission for each permission name in the list
    private static function createAllPermissions($permissionNames) {
        return array_map(function ($p) {return self::createPermission($p);}, $permissionNames);
    }

    public static function ConferenceManager($confId) {
        return DB::transaction(function() use ($confId) {
            $permissionList = [
                PermissionNames::ConferenceEventCreate($confId),
                PermissionNames::ConferenceRegistrationApproval($confId),
                PermissionNames::ConferenceInfoEdit($confId)];

            $permissions = self::createAllPermissions($permissionList);

            $rolename = RoleNames::ConferenceManager($confId);

            $role = new Role;
            $role->name = $rolename;
            $role->save();
            $role->attachPermissions($permissions);

            return $role;
        });
    }
}
