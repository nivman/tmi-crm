<?php


namespace App\Services\MassTaskAction;


use Illuminate\Support\Facades\DB;

class DeleteTasks
{
    public function delete($tasksIds)
    {
         DB::table('tasks')
            ->whereIn('id', $tasksIds)
            ->delete();

    }
}