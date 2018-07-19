<?php

use Illuminate\Database\Seeder;

class ProductCategoryTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Insert uncategory category
        DB::table('product_category')->insert(
            array(
                'name' => 'Uncategory',
                'description' => 'Product ที่ยังไม่ได้จัดหมวดหมู่'
            )
        );

        DB::table('products')->insert(
            array(
                'code' => 'P0001',
                'name' => 'Product ที่ฉันรักที่สุด',
                'unitName' => 'ชิ้น',
                'description' => 'รายละเอียด Product'
            )
        );

        DB::table('product_detail')->insert(
            array(
                'product_id' => 1,
                'label' => 'Color',
                'key' => 'color',
                'value' => 'red oak'
            )
        );
    }
}
