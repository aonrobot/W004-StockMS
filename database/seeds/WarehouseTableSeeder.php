<?php

use Illuminate\Database\Seeder;

class WarehouseTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Insert main warehouse   
        DB::table('warehouse')->insert(
            array(
                'name' => 'สาขาหลัก (Main warehouse)',
                'address' => 'ที่อยู่ สาขาหลัก',
                "created_at" =>  \Carbon\Carbon::now(),
                "updated_at" => \Carbon\Carbon::now()
            )
        );

        // Insert ​warehouse 2
        DB::table('warehouse')->insert(
            array(
                'name' => 'สาขาย่อย 1',
                'address' => 'ที่อยู่ สาขาย่อย 1',
                "created_at" =>  \Carbon\Carbon::now(),
                "updated_at" => \Carbon\Carbon::now()
            )
        );

        // Insert ​product to warehouse
        DB::table('product_has_warehouse')->insert(array('product_id' => 1, 'warehouse_id' => 1));
        DB::table('product_has_warehouse')->insert(array('product_id' => 1, 'warehouse_id' => 2));

        // Insert Inventory
        DB::table('inventory')->insert(array(
            'product_id' => 1,
            'warehouse_id' => 1,
            'quantity' => 12,
            'minLevel' => 0,
            'maxLevel' => 0,
            'costPrice' => 100.54,
            'salePrice' => 123.54,
            "created_at" =>  \Carbon\Carbon::now(),
            "updated_at" => \Carbon\Carbon::now()
        ));
        DB::table('inventory')->insert(array(
            'product_id' => 1,
            'warehouse_id' => 2,
            'quantity' => 3,
            'minLevel' => 0,
            'maxLevel' => 0,
            'costPrice' => 100.54,
            'salePrice' => 123.54,
            "created_at" =>  \Carbon\Carbon::now(),
            "updated_at" => \Carbon\Carbon::now()
        ));
    }
}
