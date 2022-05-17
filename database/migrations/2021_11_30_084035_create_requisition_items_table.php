<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRequisitionItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('requisition_items', function (Blueprint $table) {
            $table->id();
            $table->integer('requisition_id');
            $table->integer('product_id');
            $table->double('qty');
            $table->double('purchase_qty')->default(0)->nullable();
            $table->integer('type_id');
            $table->longText('comment')->nullable();
            $table->enum('is_send',array('no','yes'))->default('no')->nullable();
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
        Schema::dropIfExists('requisition_items');
    }
}
