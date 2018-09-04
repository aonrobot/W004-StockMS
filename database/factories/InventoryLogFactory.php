<?php

use Faker\Generator as Faker;

$factory->define(App\InventoryLog::class, function (Faker $faker) {

    $type = ['increase', 'decrease'];

    return [
        'inventory_id' => 1, 
        'type' => array_random($type),
        'amount' => 4,
        'remark' => str_random(40),
        'log_date' => $faker->date(),
        'log_time' => $faker->time()
    ];
});
