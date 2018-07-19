<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(UsersTableSeeder::class);
        $this->call(ProductCategoryTableSeeder::class);
        $this->call(WarehouseTableSeeder::class);
        exec('php artisan passport:install');
    }
}
