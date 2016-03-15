<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use App\User;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        DB::table('users')->delete();

        $users = array(
                ['firstName' => 'Ryan',
                 'lastName' => 'Chenkie',
                 'dateOfBirth' => '1948-01-03',
                 'gender' => '',
                 'location' => '',
                 'notes' => '',
                 'accountID' => 1,
                 'approved' => true],

                ['firstName' => 'Ryan\'s',
                 'lastName' => 'Dependent1',
                 'dateOfBirth' => '1990-01-23',
                 'gender' => '',
                 'location' => '',
                 'notes' => '',
                 'accountID' => 1,
                 'approved' => true],

                ['firstName' => 'Ryan\'s',
                 'lastName' => 'Dependent2',
                 'dateOfBirth' => '1992-05-26',
                 'gender' => '',
                 'location' => '',
                 'notes' => '',
                 'accountID' => 1,
                 'approved' => true],

                ['firstName' => 'Ryan\'s',
                 'lastName' => 'Dependent3',
                 'dateOfBirth' => '1993-10-31',
                 'gender' => '',
                 'location' => '',
                 'notes' => '',
                 'accountID' => 1,
                 'approved' => true],

                ['firstName' => 'Chris',
                 'lastName' => 'Somelastname',
                 'dateOfBirth' => '1990-01-23',
                 'gender' => '',
                 'location' => '',
                 'notes' => '',
                 'accountID' => 2,
                 'approved' => true],

                ['firstName' => 'Chris\'s',
                 'lastName' => 'Dependent1',
                 'dateOfBirth' => '1996-01-08',
                 'gender' => '',
                 'location' => '',
                 'notes' => '',
                 'accountID' => 2,
                 'approved' => false]
        );

        // Loop through each user above and create the record for them in the database
        foreach ($users as $u)
        {
            User::create($u);
        }

        Model::reguard();
    }
}
