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
                'address' => '1004/142 Nirun Residence 9, floor 6, building C,  Sukhumwit Road, Bang Na, Bang Na, Bangkok 10260'
            )
        );

        // Insert ​warehouse 2
        DB::table('warehouse')->insert(
            array(
                'name' => 'สาขาย่อย 1',
                'address' => '522/123 หมู่ 10 ต.สันทราน อ.เมือง จ.เชียงราย 57000'
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
        ));
        DB::table('inventory')->insert(array(
            'product_id' => 1,
            'warehouse_id' => 2,
            'quantity' => 3,
            'minLevel' => 0,
            'maxLevel' => 0,
            'costPrice' => 100.54,
            'salePrice' => 123.54,
        ));
    }
}
