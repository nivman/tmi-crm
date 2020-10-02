<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->string('reference', 55);
            $table->string('discount', 20)->nullable();
            $table->decimal('discount_amount', 15, 4)->default('0')->nullable();
            $table->decimal('total_tax_amount', 15, 4)->default('0')->nullable();
            $table->decimal('order_tax_amount', 15, 4)->default('0')->nullable();
            $table->decimal('product_tax_amount', 15, 4)->default('0')->nullable();
            $table->decimal('recoverable_tax_amount', 15, 4)->default('0')->nullable();
            $table->decimal('recoverable_tax_calculated_on', 15, 4)->default('0')->nullable();
            $table->decimal('shipping', 15, 4)->nullable();
            $table->decimal('total', 25, 4);
            $table->decimal('grand_total', 25, 4);
            $table->bigInteger('customer_id')->unsigned();
            $table->foreign('customer_id')->references('id')->on('customers');
            $table->bigInteger('user_id')->unsigned()->nullable();
            $table->foreign('user_id')->references('id')->on('users');
            $table->text('note')->nullable();
            $table->string('hash')->nullable();
            $table->boolean('draft')->default('0')->nullable();
            $table->bigInteger('transaction_id')->unsigned()->nullable();
            $table->bigInteger('company_id')->unsigned()->default('1');
            $table->foreign('company_id')->references('id')->on('companies');
            $table->timestamps();
        });

        Schema::create('invoice_items', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('invoice_id')->unsigned();
            $table->foreign('invoice_id')->references('id')->on('invoices');
            $table->bigInteger('product_id')->unsigned()->nullable();
            $table->string('code');
            $table->string('name');
            $table->decimal('qty', 15, 4);
            $table->decimal('price', 25, 4);
            $table->decimal('net_price', 25, 4);
            $table->decimal('unit_price', 25, 4);
            $table->decimal('subtotal', 25, 4);
            $table->string('item_taxes')->nullable();
            $table->decimal('tax_amount', 15, 4)->nullable();
            $table->decimal('total_tax_amount', 15, 4)->nullable();
            $table->string('discount')->nullable();
            $table->decimal('discount_amount', 15, 4)->nullable();
            $table->decimal('total_discount_amount', 15, 4)->nullable();
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
        Schema::dropIfExists('invoice_items');
        Schema::dropIfExists('invoices');
    }
}
