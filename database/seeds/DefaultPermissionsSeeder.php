<?php

use Illuminate\Database\Seeder;
use App\Models\Permission;
use App\Utility\PermissionNames;

//Creates global permissions along with single-permission roles for
//those permissions
class DefaultPermissionsSeeder extends Seeder
{

    private static function pArray($name, $display) {
        return ["name" => $name, "display_name" => $display];
    }
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('permissions')->delete();

        /**
         * Permission Attributes
         *
         * name:    Unique name for the permission, used for looking up permission information in the
         *             application layer. For example: "create-post", "edit-user", "post-payment", "mailing-list-subscribe".
         *
         * display_name:    Human readable name for the permission. Not necessarily unique, and is optional.
         *                     For example "Create Posts", "Edit Users", "Post Payments", "Subscribe to mailing list".
         *
         * description:     A more detailed explanation of the Permission. This is also optional.
         */

        $permissions = array(
            self::pArray(PermissionNames::CreateConference(), "Create Conference"),
            self::pArray(PermissionNames::ManageGlobalPermissions(), "Manage Global Permissions"),
            self::pArray(PermissionNames::ApproveUserRegistration(), "Approve User Registration"),
            self::pArray(PermissionNames::ViewSiteStatistics(), "View Site Statistics")
        );

        foreach ($permissions as $p) {
            $entry = new Permission();
            $entry->name = $p['name'];

            if (array_key_exists('display_name', $p))
                $entry->display_name = $p['display_name'];

            if (array_key_exists('description', $p))
                $entry->description = $p['description'];

            $entry->save();
        }
    }
}
