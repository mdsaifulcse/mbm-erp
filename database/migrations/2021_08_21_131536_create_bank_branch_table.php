<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBankBranchTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bank_branch', function (Blueprint $table) {
            $table->id();
            $table->integer('bank_id',false,11);
            $table->string('branch_code',10)->nullable();
            $table->string('branch_name',50)->nullable();
            $table->integer('routing_no',false,11)->default('0');
            $table->tinyInteger('status',false,11)->default('1');
            $table->integer('created_by',false,11)->nullable();
            $table->integer('updated_by',false,11)->nullable();
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
        Schema::dropIfExists('bank_branch');
    }
}
