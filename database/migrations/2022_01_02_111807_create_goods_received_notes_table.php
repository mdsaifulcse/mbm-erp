<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGoodsReceivedNotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('goods_received_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_order_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->string('reference_no');
            $table->double('total_price',10,2)->default(0);
            $table->float('discount',8,2)->default(0);
            $table->float('vat',8,2)->default(0);
            $table->double('gross_price',10,2)->default(0);
            $table->enum('received_status',['partial','full']);
            $table->enum('is_sent_to_accounts',['yes','no'])->default('no');
            $table->enum('is_supplier_rating',['yes','no'])->default('no');

            $table->string('challan');
            $table->string('challan_file')->nullable();
            $table->timestamp('received_date');
            $table->string('delivery_by')->nullable();
            $table->unsignedBigInteger('receive_by', false);
            $table->string('note')->nullable();

            $table->unsignedBigInteger('created_by', false);
            $table->unsignedBigInteger('updated_by', false)->nullable();
            $table->foreign('created_by')->references('id')->on('users')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('updated_by')->references('id')->on('users')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('receive_by')->references('id')->on('users')->cascadeOnDelete()->cascadeOnUpdate();
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
        Schema::table('goods_received_notes',function (Blueprint $table){
            $table->dropForeign(['purchase_order_id']);
            $table->dropForeign(['created_by']);
            $table->dropForeign(['updated_by']);
            $table->dropForeign(['receive_by']);
        });
        Schema::dropIfExists('goods_received_notes');
    }
}
