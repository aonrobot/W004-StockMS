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
            $table->string('name', 144);
            $table->mediumText('address')->nullable(true);
            $table->string('status', 24)->default('active');
            $table->timestamps();
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

        Schema::create('inventoryLog', function(Blueprint $table){
            $table->increments('id');
            $table->integer('inventory_id')->unsigned();
            $table->string('type', 32);
            $table->integer('amount')->default(0);
            $table->mediumText('remark')->nullable(true);
            $table->date('log_date');
            $table->time('log_time');
            $table->timestamps();

            $table->foreign('inventory_id')
            ->references('id')->on('inventory');
        });
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
