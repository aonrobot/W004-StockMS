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
        Schema::create('branchs', function (Blueprint $table) {
            $table->increments('branch_id');

            $table->string('name', 144);
            $table->mediumText('address');
            $table->string('status', 24)->default('active');
            $table->timestamps();
        });

        Schema::create('product_has_branch', function (Blueprint $table) {
            $table->integer('product_id')->unsigned();
            $table->integer('branch_id')->unsigned();

            $table->foreign('product_id')
            ->references('product_id')->on('products');
            $table->foreign('branch_id')
            ->references('branch_id')->on('branchs');
        });

        Schema::create('inventory', function (Blueprint $table) {
            $table->increments('inventory_id');
            $table->integer('product_id')->unsigned();
            $table->integer('branch_id')->unsigned();

            $table->integer('quantity');
            $table->integer('min_level')->default(0);
            $table->integer('max_level')->default(0);
            $table->decimal('price', 10, 2)->default(0.00);
            $table->string('status', 24)->default('active');
            $table->timestamps();

            $table->foreign('product_id')
            ->references('product_id')->on('product_has_branch')
            ->onDelete('cascade');
            $table->foreign('branch_id')
            ->references('branch_id')->on('product_has_branch')
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
