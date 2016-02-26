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
                ['firstName' => 'Ryan', 'lastName' => 'Chenkie', 'email' => 'ryanchenkie@gmail.com', 'password' => Hash::make('secret')],
                ['firstName' => 'Chris', 'lastName' => 'Sevilleja', 'email' => 'chris@scotch.io', 'password' => Hash::make('secret')],
                ['firstName' => 'Holly', 'lastName' => 'Lloyd', 'email' => 'holly@scotch.io', 'password' => Hash::make('secret')],
                ['firstName' => 'Adnan', 'lastName' => 'Kukic', 'email' => 'adnan@scotch.io', 'password' => Hash::make('secret')],
        );
            
        // Loop through each user above and create the record for them in the database
        foreach ($users as $user)
        {
            User::create($user);
        }

        Model::reguard();
    }
}
