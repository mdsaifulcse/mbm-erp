<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSupplierRattingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('supplier_rattings', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('supplier_id');

            $table->integer('communication',false,10)->nullable();
            $table->integer('on_time_delivery',false,10)->nullable();
            $table->integer('quality',false,10)->nullable();
            $table->integer('price',false,10)->nullable();
            $table->integer('working_year',false,10)->nullable();
            $table->integer('incident',false,10)->nullable();
            $table->integer('company_established',false,10)->nullable();

            $table->float('total_score',10,4)->nullable();
            
            $table->enum('status',array('active','inactive','cancel'))->nullable();
            $table->text('remarks')->nullable();

            $table->unsignedBigInteger('created_by', false);
            $table->unsignedBigInteger('updated_by', false)->nullable();

            $table->foreign('created_by')->references('id')->on('users')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('updated_by')->references('id')->on('users')->cascadeOnDelete()->cascadeOnUpdate();
            

            $table->softDeletes();
            $table->timestamps();

            $table->foreign('supplier_id')->references('id')->on('suppliers')->cascadeOnDelete()->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('supplier_rattings');
    }
}
