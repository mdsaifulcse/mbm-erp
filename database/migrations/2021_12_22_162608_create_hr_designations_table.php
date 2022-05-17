<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHrDesignationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hr_designation', function (Blueprint $table) {
            $table->integer('hr_designation_id',11);
            $table->bigInteger('hr_designation_emp_type');
            $table->bigInteger('parent_id')->nullable();
            $table->string('hr_designation_name')->nullable();
            $table->string('hr_designation_name_bn')->nullable();
            $table->string('designation_short_name')->nullable();
            $table->bigInteger('grade_id')->nullable();
            $table->string('hr_designation_grade')->nullable();
            $table->integer('hr_designation_position')->default(1);
            $table->boolean('hr_designation_status')->default(true);
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
        Schema::dropIfExists('hr_designations');
    }
}
