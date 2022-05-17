<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHrBonusAppHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('hr_bonus_app_history'))
        {
            Schema::create('hr_bonus_app_history', function (Blueprint $table) {
                $table->id();
                $table->integer('bonus_rule_id',false,11)->unsigned();
                $table->tinyInteger('stage', false,4);
                $table->integer('audit_by',false,11)->unsigned();
                $table->tinyInteger('status', false,4);
                $table->text('comment')->nullable();
                $table->timestamps();

//                $table->foreign('bonus_rule_id', 'FK_bonus_rule_id_from_bonus_rule_table')->references('id')->on('hr_bonus_rule');
//                $table->foreign('audit_by', 'FK_audit_by_from_users_table')->references('id')->on('hr_bonus_rule');
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
        Schema::dropIfExists('hr_bonus_app_history');
    }
}
