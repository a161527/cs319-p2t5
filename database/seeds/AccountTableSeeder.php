<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use App\Models\Account;
use App\Models\Role;

class AccountTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        DB::table('accounts')->delete();

        $accounts = array(
                ['email' => 'root@localhost', 'password' => Hash::make('admin'), 'roles' => ['owner']],
                ['email' => 'unprivileged@localhost', 'password' => Hash::make('secret')],
                ['email' => 'ryanchenkie@gmail.com', 'password' => Hash::make('secret')],
                ['email' => 'chris@scotch.io', 'password' => Hash::make('secret')],
                ['email' => 'holly@scotch.io', 'password' => Hash::make('secret')],
                ['email' => 'adnan@scotch.io', 'password' => Hash::make('secret')]
        );

        // Loop through each user above and create the record for them in the database
        foreach ($accounts as $a)
        {
            if (array_key_exists('roles', $a)) {
                $roles = $a['roles'];
                unset($a['roles']);
            }
            $user = Account::create($a);
            if (isset($roles)) {
                foreach ($roles as $r) {
                    $user->attachRole(Role::where('name', $r)->get()->first());
                }
                unset($roles);
            }
        }

        Model::reguard();
    }
}
