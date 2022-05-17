<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHrBillSpecialSettingsTable extends Migration
{
    public function up()
    {
    	if(!Schema::hasTable('hr_bill_special_settings'))
        {
	        Schema::create('hr_bill_special_settings', function (Blueprint $table) {

				$table->id();
				$table->integer('bill_setup_id',false,11);
				$table->string('adv_type',50)->nullable();
				$table->string('parameter',50)->nullable();
				$table->float('amount')->default(0);
				$table->tinyInteger('pay_type',false,4);
				$table->string('duration',20)->nullable();
				$table->date('start_date');
				$table->date('end_date')->nullable();
				$table->tinyInteger('status',false,4)->default(1);
				$table->integer('created_by',false,11)->nullable();
				$table->integer('updated_by',false,11)->nullable();
				$table->timestamps();

				//$table->unique('bill_setup_id', 'adv_type', 'parameter', 'pay_type', 'status');
//				$table->foreign('bill_setup_id', 'FK_bill_setup_id_from_bill_settings_table')->references('id')->on('hr_bill_settings');

	        });
	    }
    }

    public function down()
    {
        Schema::dropIfExists('hr_bill_special_settings');
    }
}