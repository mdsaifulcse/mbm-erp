<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersLoginActivitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users_login_activities', function (Blueprint $table) {
            $table->id();
            $table->string('user_id',32)->nullable();
            $table->string('user_agent',128)->nullable();
            $table->string('browser',128)->nullable();
            $table->string('platform',32)->nullable();
            $table->string('ip_address',32)->nullable();
            $table->string('mac_address',32)->nullable();
            $table->string('login_at',32)->nullable();
            $table->string('logout_at',32)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users_login_activities');
    }
}
