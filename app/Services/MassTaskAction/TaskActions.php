<?php


namespace App\Services\MassTaskAction;


class TaskActions
{
    public function dispatchActions($data)
    {
        switch ($data['action']) {
            case 'delete':
                (new DeleteTasks())->delete($data['ids']);
                break;
            case 'status':
                (new UpdateTasksStatus())->updateStatus($data['ids'], $data['statusId']);
                break;
        }
    }
}