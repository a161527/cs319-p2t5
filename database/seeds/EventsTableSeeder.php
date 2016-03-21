<?php

use Illuminate\Database\Seeder;

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
          ['eventName' => 'PartyNight',
           'date' => '2016-01-01',
           'startTime' => '14:00:00',
           'endTime' => '15:00:00',
           'location' => 'Beijing',
           'description' => 'A test Event 1.',
           'capacity' => 123,
           'conferenceID' => 1],

           ['eventName' => 'Speech',
            'date' => '2017-01-01',
            'startTime' => '12:00:00',
            'endTime' => '12:30:00',
            'location' => 'Mumbai',
            'description' => 'A test Event 2.',
            'capacity' => 123,
            'conferenceID' => 1],
      ]);
    }
}
