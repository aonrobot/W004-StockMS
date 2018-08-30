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

        // Insert ​product to warehouse
        DB::table('product_has_warehouse')->insert(array('product_id' => 1, 'warehouse_id' => 1));

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

        // $doc_id = DB::table('documentDetail')->insert(array(
        //     'number' => 1,
        //     'customer_id' => 0,
        //     'ref_id' => 0, 
        //     'source_wh_id' => 1,
        //     'target_wh_id' => null,
        //     'type' => 'inv',
        //     'tax_type' => 'with_no_tax',
        //     'comment' => 'no comment',
        //     'status' => 'create',
        //     'date' => \Carbon\Carbon::now()->toDateString()
        // ));

        // DB::table('documentLineItems')->insert(array(
        //     'document_id' => $doc_id,
        //     'product_id' => 1,
        //     'amount' => 100,
        //     'price' => 12.3,
        //     'discount' => 0,
        //     'total' => 1230
        // ));
    }
}
