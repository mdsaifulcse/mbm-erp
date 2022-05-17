<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHrBillTypeTable extends Migration
{
    public function up()
    {
    	if(!Schema::hasTable('hr_bill_type'))
        {
	        Schema::create('hr_bill_type', function (Blueprint $table) {
				$table->id();
				$table->string('name',256)->unique();
				$table->string('bangla_name',100)->nullable();
				$table->string('status',11)->nullable();
				$table->timestamp('deleted_at')->nullable();
				$table->string('created_by',11)->nullable();
				$table->string('updated_by',11)->nullable();
				$table->timestamps();

	        });
	    }
    }

    public function down()
    {
        Schema::dropIfExists('hr_bill_type');
    }
}