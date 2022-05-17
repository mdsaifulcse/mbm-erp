<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuotationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quotations', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('supplier_id');
            $table->unsignedBigInteger('request_proposal_id');

            $table->string('reference_no',32)->unique();
            $table->timestamp('quotation_date')->useCurrent();
           
            $table->double('total_price')->default(0);
            $table->double('discount')->default(0);
            $table->double('vat')->default(0);
            $table->double('gross_price')->default(0);

            $table->enum('status',array('active','inactive','cancel'))->nullable();
            
            $table->enum('type',array('manual','online','direct-purchase'))->default('manual')->nullable();
            $table->enum('is_approved',array('pending','approved','halt'))->default('pending')->nullable();
            $table->text('remarks')->nullable();
            $table->text('note')->nullable();

            $table->unsignedBigInteger('created_by', false)->nullable();
            $table->unsignedBigInteger('updated_by', false)->nullable();

            $table->foreign('created_by')->references('id')->on('users')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('updated_by')->references('id')->on('users')->cascadeOnDelete()->cascadeOnUpdate();
            

            $table->softDeletes();
            $table->timestamps();

            $table->foreign('supplier_id')->references('id')->on('suppliers')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('request_proposal_id')->references('id')->on('request_proposals')->cascadeOnDelete()->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('quotations');
    }
}
