<?php

use Illuminate\Database\Seeder;


class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(AccountTableSeeder::class);
        $this->call(DefaultPermissionsSeeder::class);
        $this->call(DefaultRolesSeeder::class);
        $this->call(UserTableSeeder::class);
        $this->call(ConferenceTableSeeder::class);
    }
}
