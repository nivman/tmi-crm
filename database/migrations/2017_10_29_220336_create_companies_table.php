<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCompaniesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('logo');
            $table->string('email');
            $table->string('phone');
            $table->string('address');
            $table->string('number')->nullable();
            $table->string('template')->default('simple');
            $table->string('footer')->nullable();
            $table->text('extra')->nullable();
            $table->string('state')->nullable();
            $table->string('country')->nullable();
            $table->string('state_name')->nullable();
            $table->string('country_name')->nullable();
            $table->boolean('show_discount')->nullable();
            $table->boolean('show_tax')->nullable()->default('1');
            $table->boolean('show_image')->nullable()->default('1');
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
        Schema::dropIfExists('companies');
    }
}
