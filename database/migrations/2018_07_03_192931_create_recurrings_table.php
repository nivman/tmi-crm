<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRecurringsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('recurrings', function (Blueprint $table) {
            $table->id();
            $table->date('start_date');
            $table->string('repeat');
            $table->string('reference', 55);
            $table->integer('create_before')->default('0');
            $table->decimal('discount_amount', 15, 4)->default('0')->nullable();
            $table->decimal('total_tax_amount', 15, 4)->default('0')->nullable();
            $table->decimal('order_tax_amount', 15, 4)->default('0')->nullable();
            $table->decimal('product_tax_amount', 15, 4)->default('0')->nullable();
            $table->decimal('shipping', 15, 4)->nullable();
            $table->string('discount', 20)->nullable();
            $table->decimal('total', 25, 4);
            $table->decimal('grand_total', 25, 4);
            $table->bigInteger('customer_id')->unsigned();
            $table->foreign('customer_id')->references('id')->on('customers');
            $table->bigInteger('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
            $table->date('last_created_at')->nullable();
            $table->boolean('active')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();
        });

        Schema::create('recurring_items', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('recurring_id')->unsigned();
            $table->foreign('recurring_id')->references('id')->on('recurrings');
            $table->bigInteger('product_id')->unsigned()->nullable();
            $table->foreign('product_id')->references('id')->on('products');
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
        Schema::dropIfExists('recurring_items');
        Schema::dropIfExists('recurrings');
    }
}
