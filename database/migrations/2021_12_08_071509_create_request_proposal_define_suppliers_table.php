<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRequestProposalDefineSuppliersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('request_proposal_define_suppliers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('request_proposal_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->unsignedBigInteger('supplier_id',false);
            $table->foreign('supplier_id')->references('id')->on('suppliers')->cascadeOnDelete()->cascadeOnUpdate();
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
        Schema::dropIfExists('request_proposal_define_suppliers');
    }
}
