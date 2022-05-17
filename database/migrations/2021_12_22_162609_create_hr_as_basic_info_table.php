<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHrAsBasicInfoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hr_as_basic_info', function (Blueprint $table) {
            $table->integer('as_id',11);
            $table->bigInteger('as_emp_type_id')->nullable();
            $table->bigInteger('as_designation_id')->nullable();
            $table->bigInteger('as_unit_id')->nullable();
            $table->bigInteger('as_location')->nullable();
            $table->string('as_floor_id')->nullable();
            $table->string('as_line_id')->nullable();
            $table->string('as_shift_id')->nullable();
            $table->bigInteger('as_area_id')->nullable();
            $table->bigInteger('as_department_id')->nullable();
            $table->bigInteger('as_section_id')->nullable();
            $table->bigInteger('as_subsection_id')->nullable();
            $table->timestamp('as_doj')->useCurrent();
            $table->string('associate_id')->nullable();
            $table->string('temp_id')->nullable();
            $table->string('as_name',64)->nullable();
            $table->string('as_gender')->nullable();
            $table->timestamp('as_dob')->useCurrent()->nullable();
            $table->string('as_contact')->nullable();
            $table->tinyInteger('as_ot',false,4)->default(0);
            $table->string('as_pic',255)->nullable();

            $table->tinyInteger('as_status',false,4)->default(1)->comment('0-delete, 1-active, 2-resign, 3-terminate, 4-suspend, 5-left, 6-maternity');

            $table->timestamp('as_status_date')->useCurrent()->nullable();
            $table->text('as_remarks')->nullable();
            $table->string('as_rfid_code')->nullable();
            $table->string('as_oracle_code')->nullable();
            $table->string('as_oracle_sl')->nullable();
            $table->string('unit_temp')->nullable();
            $table->tinyInteger('shift_roaster_status',false,4)->default(0);
            $table->bigInteger('worker_id')->nullable();

            $table->bigInteger('created_by')->nullable();
            $table->bigInteger('updated_by')->nullable();
            $table->softDeletes('deleted_at')->nullable();
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
        Schema::dropIfExists('hr_as_basic_info');
    }
}
