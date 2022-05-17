<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *'name', 'category_id', 'brand_id', 'tax', 'unit'
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('category_id');
            $table->bigInteger('brand_id');
            $table->string('name');
            $table->string('tax');
            $table->string('unit');
                $table->string('sku',64)->nullable();
            $table->decimal('unit_price')->default(0);
            $table->unsignedBigInteger('product_unit_id', false)->nullable();
            $table->foreign('product_unit_id')->references('id')->on('product_units')->cascadeOnDelete()->cascadeOnUpdate();
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

        Schema::table('product_units', function (Blueprint $table) {
            $table->dropForeign(['product_unit_id']);
        });

        Schema::dropIfExists('products');
    }
}
