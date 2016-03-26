<?php

use Illuminate\Database\Seeder;
use App\Event;
use App\Utility\RoleCreate;
use App\Models\Account;

class EventsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      DB::table('events')->insert([
          ['id' => '1',
           'eventName' => 'PartyNight',
           'date' => '2016-01-01',
           'startTime' => '14:00:00',
           'endTime' => '15:00:00',
           'location' => 'Beijing',
           'description' => 'A test Event 1.',
           'capacity' => 123,
           'conferenceID' => 1],

           ['id' => '2',
            'eventName' => 'Speech',
            'date' => '2017-01-01',
            'startTime' => '12:00:00',
            'endTime' => '12:30:00',
            'location' => 'Mumbai',
            'description' => 'A test Event 2.',
            'capacity' => 123,
            'conferenceID' => 1],
      ]);
      $role = RoleCreate::AllEventRoles(1);
      Account::where('email', 'root@localhost')->get()->first()->attachRole($role);

      $role = RoleCreate::AllEventRoles(2);
      Account::where('email', 'root@localhost')->get()->first()->attachRole($role);
    }
}
