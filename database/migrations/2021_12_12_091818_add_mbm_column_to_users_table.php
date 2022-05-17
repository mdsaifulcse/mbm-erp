<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMbmColumnToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('associate_id');
            $table->tinyInteger('password_request',false,4)->default(0);
            $table->string('unit_permissions',64)->nullable();
            $table->string('buyer_permissions',64)->nullable();
            $table->string('management_restriction',64)->nullable();
            $table->integer('buyer_template_permission',false,11)->nullable();
            $table->string('location_permission',100)->nullable();
            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->string('deleted_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
}
