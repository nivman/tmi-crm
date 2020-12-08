<?php


namespace App\Services\MassTaskAction;


use Illuminate\Support\Facades\DB;

class UpdateTasksStatus
{
    public function updateStatus($tasksIds, $statusId)
    {
        DB::table('tasks')
            ->whereIn('id', $tasksIds)
            ->update(['status_id' => $statusId]);
    }
}