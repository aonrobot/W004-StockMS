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
            $table->integer('product_id')->unsigned();
            $table->integer('warehouse_id')->unsigned();

            $table->integer('quantity')->default(0);
            $table->integer('minLevel')->default(0);
            $table->integer('maxLevel')->default(0);
            $table->decimal('costPrice', 10, 2)->default(0.00);
            $table->decimal('salePrice', 10, 2)->default(0.00);
            $table->string('status', 24)->default('active');
            $table->timestamps();

            $table->foreign('product_id')
            ->references('product_id')->on('product_has_warehouse')
            ->onDelete('cascade');
            $table->foreign('warehouse_id')
            ->references('warehouse_id')->on('product_has_warehouse')
            ->onDelete('cascade');
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
