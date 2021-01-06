<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTasksEventsRepeatRules extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tasks_events_repeat_rules', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->json('rules');
            $table->bigInteger('task_id')->unsigned()->nullable();
            $table->foreign('task_id')->references('id')->on('tasks');
            $table->bigInteger('event_id')->unsigned()->nullable();
            $table->foreign('event_id')->references('id')->on('events');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tasks_events_repeat_rules');
    }
}
