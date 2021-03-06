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
        $this->call(DefaultPermissionsSeeder::class);
        $this->call(DefaultRolesSeeder::class);
        $this->call(AccountTableSeeder::class);
        $this->call(UserTableSeeder::class);
        $this->call(ConferenceTableSeeder::class);
        $this->call(EventsTableSeeder::class);
        $this->call(RoomSeeder::class);
        $this->call(InventorySeeder::class);
        $this->call(UserInventorySeeder::class);
    }
}
