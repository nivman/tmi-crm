<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTaxesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('taxes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code', 20)->unique();
            $table->decimal('rate', 8, 2)->nullable();
            $table->text('details')->nullable();
            $table->string('number')->nullable();
            $table->boolean('state')->default('0');
            $table->boolean('same')->default('0');
            $table->boolean('compound')->default('0');
            $table->boolean('recoverable')->default('0');
            $table->timestamps();
        });

        Schema::create('taxables', function (Blueprint $table) {
            $table->integer('tax_id');
            $table->integer('taxable_id');
            $table->string('taxable_type');
            $table->decimal('amount', 15, 4)->nullable();
            $table->decimal('total_amount', 15, 4)->nullable();
            $table->decimal('calculated_on', 15, 4)->nullable();
            $table->boolean('recoverable')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('taxes');
        Schema::dropIfExists('taxables');
    }
}
