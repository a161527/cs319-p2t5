<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use App\Models\Account;

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
                ['email' => 'ryanchenkie@gmail.com', 'password' => Hash::make('secret')],
                ['email' => 'chris@scotch.io', 'password' => Hash::make('secret')],
                ['email' => 'holly@scotch.io', 'password' => Hash::make('secret')],
                ['email' => 'adnan@scotch.io', 'password' => Hash::make('secret')],
        );
            
        // Loop through each user above and create the record for them in the database
        foreach ($accounts as $a)
        {
            Account::create($a);
        }

        Model::reguard();
    }
}
