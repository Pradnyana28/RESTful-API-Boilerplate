<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\User;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')
            ->insert([
                [
                    'id_group' => 3,
                    'name' => 'Guest',
                    'email' => 'guest@guest.com',
                    'phone' => '12345',
                    'password' => User::generatePassword(rand(12345,54321))
                ],
                [
                    'id_group' => 1,
                    'name' => 'Operator',
                    'email' => 'info@operator.com',
                    'phone' => '12345',
                    'password' => User::generatePassword('Operator28@')
                ]
            ]);
    }
}
