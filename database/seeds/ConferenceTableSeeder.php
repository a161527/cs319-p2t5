<?php

use Illuminate\Database\Seeder;
use App\Conference;

class ConferenceTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $conferences = [
            ['conferenceName' => 'Foo',
             'dateStart' => '2016-01-01',
             'dateEnd' => '2016-02-01',
             'location' => 'Earth',
             'description' => 'A test conference.',
             'hasTransportation' => true,
             'hasAccommodations' => false],

            ['conferenceName' => 'Bar',
             'dateStart' => '2016-02-03',
             'dateEnd' => '2016-02-05',
             'location' => 'Earth',
             'description' => 'A test conference number 2.',
             'hasTransportation' => true,
             'hasAccommodations' => true]
        ];
        foreach ($conferences as $conf){
            Conference::create($conf);
        }
    }
}
