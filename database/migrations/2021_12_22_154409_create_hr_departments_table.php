<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHrDepartmentsTable extends Migration
{
    /**
     * Run the migrations.
     *'hr_department_id', 'hr_department_area_id', 'hr_department_name', 'hr_department_name_bn', 'hr_department_code', 'hr_department_min_range', 'hr_department_max_range', 'hr_department_status', 'sequence', 'created_by', 'updated_by', 'deleted_at'
     * @return void
     */
    public function up()
    {
        Schema::create('hr_department', function (Blueprint $table) {
            $table->integer('hr_department_id',11);
            $table->bigInteger('hr_department_area_id')->nullable();
            $table->string('hr_department_name');
            $table->string('hr_department_name_bn')->nullable();
            $table->string('hr_department_code')->nullable();
            $table->string('hr_department_min_range')->nullable();
            $table->string('hr_department_max_range')->nullable();
            $table->boolean('hr_department_status')->default(true);
            $table->integer('sequence')->default(10);
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
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
        Schema::dropIfExists('hr_departments');
    }
}
