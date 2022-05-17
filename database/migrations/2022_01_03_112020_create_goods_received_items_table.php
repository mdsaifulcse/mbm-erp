<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGoodsReceivedItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('goods_received_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('goods_received_note_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->decimal('unit_amount')->default(0);
            $table->decimal('qty')->default(0);
            $table->decimal('sub_total')->default(0);
            $table->decimal('discount')->default(0);
            $table->decimal('vat')->default(0);
            $table->decimal('total_amount')->default(0);

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
        Schema::table('goods_received_items',function (Blueprint $table){
            $table->dropForeign(['goods_received_note_id']);
            $table->dropForeign(['product_id']);
        });
        Schema::dropIfExists('goods_received_items');
    }
}
