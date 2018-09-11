<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInventoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('warehouse', function (Blueprint $table) {
            $table->increments('warehouse_id');
            $table->integer('user_id')->unsigned();
            $table->string('name', 144);
            $table->mediumText('address')->nullable(true);
            $table->string('status', 24)->default('active');
            $table->timestamps();

            $table->foreign('user_id')
            ->references('id')->on('users');
        });

        Schema::create('product_has_warehouse', function (Blueprint $table) {
            $table->integer('product_id')->unsigned();
            $table->integer('warehouse_id')->unsigned();

            $table->foreign('product_id')
            ->references('product_id')->on('products');
            $table->foreign('warehouse_id')
            ->references('warehouse_id')->on('warehouse');
        });

        Schema::create('inventory', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('product_id')->unsigned();
            $table->integer('warehouse_id')->unsigned();
            $table->integer('quantity')->default(0);
            $table->integer('minLevel')->default(0);
            $table->integer('maxLevel')->default(0);
            $table->decimal('costPrice', 10, 2)->default(0.00);
            $table->decimal('salePrice', 10, 2)->default(0.00);
            $table->string('status', 24)->default('active');
            $table->timestamps();

            $table->foreign('product_id', 'warehouse_id')
            ->references('product_id', 'warehouse_id')->on('product_has_warehouse')
            ->onDelete('cascade');
        });

        /* 
            INV : Invoice (have amount detail, )
            PO : Purchase Order (have detail)
            TF : Tranfer (have detail)
            CN :
            DN :
            RGA :

            status
                1. wait
                2. complete
                3. cancel
        */

        Schema::create('documentDetail', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();

            $table->string('number');
            $table->integer('customer_id')->unsigned()->nullable(true);
            $table->integer('ref_id')->unsigned()->nullable(true);       
            $table->integer('source_wh_id')->unsigned()->nullable(true);
            $table->integer('target_wh_id')->unsigned()->nullable(true);

            $table->string('type', 32);
            $table->string('tax_type', 32)->nullable(true)->default('withoutTax');
            $table->mediumText('comment')->nullable(true);
            $table->string('status', 24);

            $table->date('date');
            $table->timestamps();

            $table->foreign('user_id')
            ->references('id')->on('users');
        });

        Schema::create('documentLineItems', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('document_id')->unsigned()->nullable(true);
            $table->integer('product_id')->unsigned();

            $table->integer('amount')->default(0);
            $table->decimal('price', 10, 2)->default(0.00);
            $table->decimal('discount', 10, 2)->default(0.00);
            $table->decimal('total', 10, 2)->default(0.00);

            //$table->integer('quantity')->default(0);

            $table->timestamps();

            $table->foreign('document_id')
            ->references('id')->on('documentDetail')
            ->onDelete('cascade');
        });

        Schema::create('transaction', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('document_id')->unsigned();
            $table->integer('lineitem_id')->unsigned();
            $table->integer('product_id')->unsigned();
            $table->string('type', 32);
            $table->string('status', 32);
            $table->integer('source_wh_id')->unsigned()->nullable(true);
            $table->integer('target_wh_id')->unsigned()->nullable(true);
            $table->integer('amount')->default(0);
            $table->integer('balance')->default(0);
            $table->timestamps();
        });

        // Schema::create('inventoryLog', function(Blueprint $table){
        //     $table->increments('id');
        //     $table->integer('product_id')->unsigned();
        //     $table->integer('document_id')->unsigned();
        //     $table->integer('from_wh_id')->unsigned();
        //     $table->integer('to_wh_id')->unsigned();
        //     $table->string('type', 32);
        //     $table->integer('amount')->default(0);
        //     $table->integer('quantity')->default(0);
        //     $table->mediumText('remark')->nullable(true);
        //     $table->date('log_date');
        //     $table->time('log_time');
        //     $table->timestamps();

        //     $table->foreign('inventory_id')
        //     ->references('id')->on('inventory');
        // });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('inventory');
    }
}
