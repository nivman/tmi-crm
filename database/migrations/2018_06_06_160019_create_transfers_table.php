<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTransfersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transfers', function (Blueprint $table) {
            $table->id();
            $table->decimal('amount', 25, 4);
            $table->text('details')->nullable();
            $table->string('to_transaction_id')->nullable();
            $table->string('from_transaction_id')->nullable();
            $table->bigInteger('to')->unsigned()->nullable();
            $table->foreign('to')->references('id')->on('accounts');
            $table->bigInteger('from')->unsigned();
            $table->foreign('from')->references('id')->on('accounts');
            $table->bigInteger('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
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
        Schema::dropIfExists('transfers');
    }
}
