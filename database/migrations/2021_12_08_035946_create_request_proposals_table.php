<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRequestProposalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('request_proposals', function (Blueprint $table) {
            $table->id();
            $table->string('reference_no',32)->unique();
            $table->timestamp('request_date')->useCurrent();
            $table->text('remarks')->nullable();

            $table->string('status')->default(\App\Models\PmsModels\Rfp\RequestProposal::ACTIVE);
            $table->enum('type',array('manual','online','direct-purchase'))->default('manual')->nullable();

            $table->unsignedBigInteger('created_by', false);
            $table->unsignedBigInteger('updated_by', false)->nullable();
            $table->foreign('created_by')->references('id')->on('users')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('updated_by')->references('id')->on('users')->cascadeOnDelete()->cascadeOnUpdate();
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
        Schema::dropIfExists('request_proposals');
    }
}
