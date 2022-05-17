<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRequestProposalDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('request_proposal_details', function (Blueprint $table) {

            $table->id();
            $table->foreignId('request_proposal_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('requisition_id')->nullable()->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onUpdate('cascade')->onDelete('cascade');

            $table->float('request_qty',9,1)->default(0);
            $table->float('qty',9,1)->default(0);

            $table->string('status')->default(\App\Models\PmsModels\Rfp\RequestProposalDetails::ACTIVE);

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
        Schema::dropIfExists('request_proposal_details');
    }
}
