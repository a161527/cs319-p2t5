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

    private static function findPermissions($permissionNames) {
        return Permission::whereIn("name", $permissionNames)->get();
    }

    public static function AllConferenceRoles($confId) {
        return DB::transaction(function() use ($confId) {
            $permissionList = PermissionNames::AllConferencePermissions($confId);

            $permissions = self::createAllPermissions($permissionList);
            foreach ($permissions as $p) {
                $role = new Role;
                $role->name = $p->name;
                $role->save();
                $role->attachPermission($p);
            }

            $managerRoleId = self::ConferenceManager($confId, $permissions);
            return $managerRoleId;
        });
    }

    public static function ConferenceManager($confId, $permissions) {
        $rolename = RoleNames::ConferenceManager($confId);

        $role = new Role;
        $role->name = $rolename;
        $role->save();
        $role->attachPermissions($permissions);

        return $role;
    }

    public static function EventManager($eventId) {
        return DB::transaction(function() use ($eventId) {
            $permissionList = PermissionNames::AllEventPermissions($eventId);

            $permissions = self::createAllPermissions($permissionList);

            $rolename = RoleNames::EventManager($eventId);

            $role = new Role;
            $role->name = $rolename;
            $role->save();
            $role->attachPermissions($permissions);

            return $role;
        });
    }
}
