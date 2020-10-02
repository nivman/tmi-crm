<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdatePaymentsTable extends Migration
{
    public function down()
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropForeign(['invoice_id']);
            $table->dropForeign(['purchase_id']);
            $table->dropColumn('invoice_id');
            $table->dropColumn('purchase_id');
        });
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn('paid');
            $table->dropForeign(['recurring_id']);
            $table->dropColumn('recurring_id');
        });
        Schema::table('purchases', function (Blueprint $table) {
            $table->dropColumn('paid');
        });
        Schema::dropIfExists('invoice_payment');
        Schema::dropIfExists('payment_purchase');
    }

    public function up()
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->unsignedBigInteger('invoice_id')->nullable();
            $table->unsignedBigInteger('purchase_id')->nullable();

            $table->index('invoice_id');
            $table->index('purchase_id');
            $table->foreign('invoice_id')->references('id')->on('invoices');
            $table->foreign('purchase_id')->references('id')->on('purchases');
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->boolean('paid')->nullable();
            $table->unsignedBigInteger('recurring_id')->nullable();
            $table->foreign('recurring_id')->references('id')->on('recurrings');
        });

        Schema::table('purchases', function (Blueprint $table) {
            $table->boolean('paid')->nullable();
        });

        Schema::create('invoice_payment', function (Blueprint $table) {
            $table->decimal('amount', 25, 4);
            $table->unsignedBigInteger('invoice_id')->nullable();
            $table->unsignedBigInteger('payment_id')->nullable();

            $table->index('invoice_id');
            $table->index('payment_id');
            $table->index(['invoice_id', 'payment_id']);
            $table->unique(['invoice_id', 'payment_id']);
            $table->foreign('payment_id')->references('id')->on('payments');
            $table->foreign('invoice_id')->references('id')->on('invoices');
        });

        Schema::create('payment_purchase', function (Blueprint $table) {
            $table->decimal('amount', 25, 4);
            $table->unsignedBigInteger('purchase_id')->nullable();
            $table->unsignedBigInteger('payment_id')->nullable();

            $table->index('purchase_id');
            $table->index('payment_id');
            $table->index(['purchase_id', 'payment_id']);
            $table->unique(['purchase_id', 'payment_id']);
            $table->foreign('payment_id')->references('id')->on('payments');
            $table->foreign('purchase_id')->references('id')->on('purchases');
        });
    }
}
