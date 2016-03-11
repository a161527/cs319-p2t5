<?php

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Utility\PermissionNames;
use App\Models\Permission;

class DefaultRolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('roles')->delete();

        /**
         * Role Attributes
         *
         * name:    Unique name for the permission, used for looking up permission information in the
         *             application layer. For example: "create-post", "edit-user", "post-payment", "mailing-list-subscribe".
         *
         * display_name:    Human readable name for the permission. Not necessarily unique, and is optional.
         *                     For example "Create Posts", "Edit Users", "Post Payments", "Subscribe to mailing list".
         *
         * description:     A more detailed explanation of the Role. This is also optional.
         *
         * permissions: A list of permission names to assign to the user.  Optional.
         */

        $roles = array(
            array('name'          => 'user',
                  'display_name'  => 'Basic User',
                  'description'   => 'An approved user who can view and register for conferences and events.'),
            array('name'          => 'owner',
                  'display_name'  => 'Owner',
                  'description'   => 'Owner of the management system. Has access to all aspects of the system.',
                  'permissions'   => [PermissionNames::ConferenceCreate()])
             // array(    'name'            => '',
             //         'display_name'    => '',
             //         'description'    => ''),
         );

        foreach ($roles as $r) {
            $entry = new Role();
            $entry->name = $r['name'];

            if (array_key_exists('permissions', $r)) {
                $permissions = $r['permissions'];
                unset($r['permissions']);
            }

            if (array_key_exists('display_name', $r))
                $entry->display_name = $r['display_name'];

            if (array_key_exists('description', $r))
                $entry->description = $r['description'];

            $entry->save();

            if (isset($permissions)) {
                foreach ($permissions as $p) {
                    $entry->attachPermission(Permission::where('name', $p)->get()->first());
                }
                unset($permissions);
            }
        }
    }
}
