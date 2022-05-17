<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRequisitionDeliveryItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('requisition_delivery_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('requisition_delivery_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->decimal('delivery_qty')->default(0);

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
        Schema::table('requisition_delivery_items',function (Blueprint $table){
            $table->dropForeign(['requisition_delivery_id']);
            $table->dropForeign(['product_id']);
        });

        Schema::dropIfExists('requisition_delivery_items');
    }
}
