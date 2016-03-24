<?php

use Illuminate\Database\Seeder;
use App\Conference;
use App\Utility\RoleCreate;
use App\Models\Account;

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
            ['id' => 1,
             'conferenceName' => 'Foo',
             'dateStart' => '2016-01-01',
             'dateEnd' => '2016-02-01',
             'location' => 'Earth',
             'description' => 'A test conference.',
             'hasTransportation' => true,
             'hasAccommodations' => false],

            ['id' => 2,
             'conferenceName' => 'Bar',
             'dateStart' => '2016-02-03',
             'dateEnd' => '2016-02-05',
             'location' => 'Earth',
             'description' => 'A test conference number 2.',
             'hasTransportation' => true,
             'hasAccommodations' => true]
        ];
        foreach ($conferences as $conf){
            $c = Conference::create($conf);
            $role = RoleCreate::AllConferenceRoles($c->id);
            Account::where('email', 'root@localhost')->get()->first()->attachRole($role);
        }
    }
}
