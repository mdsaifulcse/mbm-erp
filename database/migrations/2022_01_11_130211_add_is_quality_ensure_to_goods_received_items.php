<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsQualityEnsureToGoodsReceivedItems extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('goods_received_items', function (Blueprint $table) {
             $table->enum('quality_ensure',['pending','approved','return','return-change'])->default('pending');
            $table->integer('received_qty',false,10)->default(0);
             
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('goods_received_items', function (Blueprint $table) {
            $table->enum('quality_ensure',['pending','approved','return','return-change'])->default('pending');
        });
    }
}
