<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('reference', 55);
            $table->decimal('amount', 25, 4);
            $table->text('note')->nullable();
            $table->string('gateway')->nullable();
            $table->bigInteger('user_id')->unsigned()->nullable();
            $table->bigInteger('account_id')->unsigned()->nullable();
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('account_id')->references('id')->on('accounts');
            $table->bigInteger('account_transaction_id')->unsigned()->nullable();
            $table->bigInteger('payer_transaction_id')->unsigned()->nullable();
            $table->string('gateway_transaction_id')->nullable();
            $table->boolean('received')->default(0);
            $table->string('hash')->nullable();
            $table->morphs('payable');
            $table->string('file')->nullable();
            $table->boolean('review')->nullable();
            $table->bigInteger('reviewed_by')->nullable();
            $table->bigInteger('company_id')->unsigned()->default('1');
            $table->foreign('company_id')->references('id')->on('companies');
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
        Schema::dropIfExists('payments');
    }
}
