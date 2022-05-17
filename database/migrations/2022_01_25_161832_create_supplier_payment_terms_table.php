<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSupplierPaymentTermsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('supplier_payment_terms', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('supplier_id');
            $table->unsignedBigInteger('payment_term_id');
            $table->float('payment_percent',4,1)->default(0);
            $table->string('remarks')->nullable();
            $table->integer('day_duration')->default(0);
            $table->string('type',50)->default(\App\Models\PmsModels\SupplierPaymentTerm::DUE);

            $table->foreign('supplier_id')->references('id')->on('suppliers')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('payment_term_id')->references('id')->on('payment_terms')->cascadeOnDelete()->cascadeOnUpdate();

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
        Schema::table('supplier_payment_terms',function (Blueprint $table){
            $table->dropForeign(['payment_term_id']);
            $table->dropForeign(['supplier_id']);
        });

        Schema::dropIfExists('supplier_payment_terms');
    }
}
