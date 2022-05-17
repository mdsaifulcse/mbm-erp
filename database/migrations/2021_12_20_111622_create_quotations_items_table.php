<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuotationsItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quotations_items', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('quotation_id');
            $table->unsignedBigInteger('product_id');

            $table->double('unit_price')->default(0);
            $table->integer('qty',false,10)->default(0);
            $table->double('sub_total_price')->default(0);

            $table->double('discount')->default(0);
            $table->double('vat')->default(0);
            $table->double('total_price')->default(0)->comment('total_price=(subtotal-discount)+vat');

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
        Schema::dropIfExists('quotations_items');
    }
}
