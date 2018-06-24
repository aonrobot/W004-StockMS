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
            $table->integer('id')->primary();
            $table->string('name', 144)->nullable(false);
            $table->text('description');
            $table->timestamps();
        });

        Schema::create('products', function (Blueprint $table) {
            $table->integer('product_id')->primary();
            $table->string('code', 64)->unique();
            $table->string('name', 144)->nullable(false);
            $table->text('description');
            $table->string('status', 24)->default('active');
            $table->timestamps();

            $table->foreign('product_id')
            ->references('id')->on('product_category')
            ->onDelete('cascade');
        });

        Schema::create('product_detail', function (Blueprint $table) {
            $table->integer('id')->primary();
            $table->string('label', 144);
            $table->string('key', 144);
            $table->text('value');
            $table->string('type', 32)->default('textbox');
            $table->timestamps();

            $table->foreign('id')
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
