<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBankChargesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bank_charges', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('account_id')->nullable();
            //make it decimal
            $table->decimal('amount', 9,2)->nullable();
            $table->integer('created_by');
            $table->integer('customer_payment_id')->unsigned();
//            $table->foreign('customer_payment_id')->references('id')->on('customer_payments');
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
        Schema::dropIfExists('bank_charges');
    }
}
