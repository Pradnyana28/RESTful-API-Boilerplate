<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserGroupsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('user_groups')
            ->insert([
                [
                    'name' => 'operator',
                    'permissions' => json_encode([
                        'users' => 1,
                        'fastboats' => 1,
                        'packages' => 1,
                        'company' => 1,
                        'voucher' => 1,
                        'category' => 1,
                        'transaction' => 1,
                        'report' => 0
                    ])
                ],
                [
                    'name' => 'manager',
                    'permissions' => json_encode([
                        'users' => 0,
                        'fastboats' => 0,
                        'packages' => 0,
                        'company' => 0,
                        'voucher' => 0,
                        'category' => 0,
                        'transaction' => 0,
                        'report' => 1
                    ])
                ],
                [
                    'name' => 'user',
                    'permissions' => json_encode([
                        'users' => 0,
                        'fastboats' => 0,
                        'packages' => 0,
                        'company' => 0,
                        'voucher' => 0,
                        'category' => 0,
                        'transaction' => 0,
                        'report' => 0
                    ])
                ]
            ]);
    }
}
