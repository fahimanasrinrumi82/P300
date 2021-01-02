<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('product_name');
            $table->integer('category_id');
            $table->longText('product_short_description');
            $table->longText('product_long_description')->nullable();
            $table->integer('product_quantity');
            $table->integer('product_alert_quantity');
            $table->float('product_price');
            $table->string('product_thumbnail_picture')->default('default_product_thumbnail.png');
            $table->longText('slug')->nullable();
            $table->integer('create_by');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
}
