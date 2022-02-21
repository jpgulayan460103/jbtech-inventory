<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'name' => 'admin',
            'email' => 'admin',
            'password' => 'admin@123',
            'account_type' => 'admin',
        ]);
        User::create([
            'name' => 'main warehouse',
            'email' => 'mwarehouse',
            'password' => 'mwarehouse@123',
            'warehouse_id' => 1,
            'account_type' => 'warehouse_admin',
        ]);
        User::create([
            'name' => 'jb warehouse',
            'email' => 'jbwarehouse',
            'password' => 'jbwarehouse@123',
            'warehouse_id' => 2,
            'account_type' => 'warehouse_admin',
        ]);
    }
}
