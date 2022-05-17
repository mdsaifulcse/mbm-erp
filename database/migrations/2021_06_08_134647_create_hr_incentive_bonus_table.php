<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHrIncentiveBonusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('hr_incentive_bonus'))
        {
            Schema::create('hr_incentive_bonus', function (Blueprint $table) {
                $table->id();
                $table->integer('as_id')->unsigned();
                $table->date('date');
                $table->float('amount');
                $table->float('target_given')->nullable();
                $table->float('target_achieve')->nullable();
                $table->tinyInteger('pay_status')->default(0);
                $table->date('pay_date')->nullable();
                $table->integer('style_id')->unsigned()->nullable();
                $table->integer('line_id')->unsigned()->nullable();
                $table->timestamps();
            });
            // 
            Schema::table('hr_incentive_bonus', function($table)
            {
                //$table->foreign('as_id', 'FK_as_id_from_basic_table')->references('as_id')->on('hr_as_basic_info');

                //$table->foreign('line_id', 'FK_line_id_from_hr_line_table')->references('hr_line_id')->on('hr_line');
                // 
                $table->index(['as_id', 'date']);
                // 
                $table->unique('as_id', 'date');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('hr_incentive_bonus');
    }
}
