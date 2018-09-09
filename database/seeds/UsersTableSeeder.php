<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'name' => 'คุณตุ๊กตา',
            'branchName' => 'สาขา 1',
            'email' => 'admin@stockms.com',
            'password' => bcrypt('admin'),
        ]);

        DB::table('users')->insert([
            'name' => 'คุณแหม่ม',
            'branchName' => 'สาขา 2',
            'email' => 'admin2@stockms.com',
            'password' => bcrypt('admin'),
        ]);
    }
}
