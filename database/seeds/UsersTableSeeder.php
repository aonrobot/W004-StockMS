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
            'name' => 'admin@admin.com',
            'email' => 'admin@stockms.com',
            'password' => bcrypt('admin'),
        ]);
    }
}