<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHrUnitTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hr_unit', function (Blueprint $table) {
            $table->increments('hr_unit_id');
            $table->string('hr_unit_name',128)->nullable();
            $table->string('hr_unit_short_name',128)->nullable();
            $table->string('hr_unit_name_bn',255)->nullable();
            $table->string('hr_unit_address',255)->nullable();
            $table->text('hr_unit_address_bn')->nullable();
            $table->string('hr_unit_telephone',255)->nullable();
            $table->string('hr_unit_code',255)->nullable();
            $table->string('hr_unit_logo',255)->nullable();
            $table->tinyInteger('hr_unit_status',false,1)->default(1)->nullable();
            $table->string('hr_unit_att_table',255)->nullable();
            $table->string('hr_unit_reference_heading',255)->nullable();
            $table->integer('hr_unit_group',false,11)->nullable();
            $table->string('hr_unit_authorized_signature',255)->nullable();
            $table->integer('created_by',false,11)->nullable();
            $table->integer('updated_by',false,11)->nullable();
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
        Schema::dropIfExists('hr_unit');
    }
}
