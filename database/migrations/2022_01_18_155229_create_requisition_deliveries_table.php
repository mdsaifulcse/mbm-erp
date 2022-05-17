<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRequisitionDeliveriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('requisition_deliveries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('requisition_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->string('reference_no');

            $table->timestamp('delivery_date');
            $table->string('note')->nullable();

            $table->unsignedBigInteger('delivery_by', false);
            $table->unsignedBigInteger('created_by', false);
            $table->unsignedBigInteger('updated_by', false)->nullable();

            $table->foreign('created_by')->references('id')->on('users')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('updated_by')->references('id')->on('users')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('delivery_by')->references('id')->on('users')->cascadeOnDelete()->cascadeOnUpdate();
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
        Schema::table('requisition_deliveries',function (Blueprint $table){
            $table->dropForeign(['requisition_id']);
            $table->dropForeign(['created_by']);
            $table->dropForeign(['updated_by']);
            $table->dropForeign(['delivery_by']);
        });

        Schema::dropIfExists('requisition_deliveries');
    }
}
