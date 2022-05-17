<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHrLocationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hr_location', function (Blueprint $table) {
            $table->integer('hr_location_id',11);
            
            $table->string('hr_location_name',64)->nullable();
            $table->string('hr_location_short_name',64)->nullable();
            $table->string('hr_location_name_bn',255)->nullable();
            $table->text('hr_location_address')->nullable();
            $table->text('hr_location_address_bn')->nullable();
            $table->string('hr_location_code')->nullable();
            $table->tinyInteger('hr_location_status',false,4)->default(1);
            $table->bigInteger('hr_location_unit_id')->nullable();

            $table->bigInteger('created_by')->nullable();
            $table->bigInteger('updated_by')->nullable();
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
        Schema::dropIfExists('hr_location');
    }
}
