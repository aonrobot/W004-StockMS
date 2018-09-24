<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_category', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            
            $table->string('name', 144)->nullable(false);
            $table->longText('description')->nullable(true);
            $table->timestamps();
        });

        Schema::create('products', function (Blueprint $table) {
            $table->increments('product_id');
            $table->integer('user_id')->unsigned();
            $table->integer('category_id')->unsigned()->default(1);

            // for branch must remove unique
            $table->string('code', 64);
            $table->string('name', 144)->nullable(false);
            $table->string('unitName', 144)->default(NULL);
            $table->longText('description')->nullable(true);
            $table->string('status', 24)->default('active');
            $table->timestamps();

            $table->foreign('category_id')
            ->references('id')->on('product_category')
            ->onDelete('cascade');

            $table->foreign('user_id')
            ->references('id')->on('users');
        });

        Schema::create('product_detail', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('product_id')->unsigned();

            $table->string('label', 144);
            $table->string('key', 144);
            $table->text('value')->nullable(true);
            $table->string('type', 32)->default('textbox');
            $table->timestamps();

            $table->foreign('product_id')
            ->references('product_id')->on('products')
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
        Schema::dropIfExists('product_detail');
        Schema::dropIfExists('products');
        Schema::dropIfExists('product_category');

    }
}
