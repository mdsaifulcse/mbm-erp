<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHrBillSettingsTable extends Migration
{
    public function up()
    {
    	if(!Schema::hasTable('hr_bill_settings'))
        {
	        Schema::create('hr_bill_settings', function (Blueprint $table) {
				$table->id();
				$table->integer('unit_id',false,11);
				$table->string('code',20)->nullable();
				$table->integer('bill_type_id',false,11);
				$table->float('amount');
				$table->tinyInteger('pay_type',false,11);
				$table->string('duration',20);
				$table->tinyInteger('as_ot',false,11);
				$table->date('start_date');
				$table->date('end_date')->nullable();
				$table->tinyInteger('status',false,11)->default(1);
				$table->integer('created_by',false,11)->nullable();
				$table->integer('updated_by',false,11)->nullable();
				$table->timestamps();
				
				//$table->unique('unit_id', 'code', 'bill_type_id', 'status');
				//$table->foreign('bill_type_id')->references('id')->on('hr_bill_type');

	        });
	    }
    }

    public function down()
    {
        Schema::dropIfExists('hr_bill_settings');
    }
}