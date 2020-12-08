<?php


namespace App\Services;


use App\Services\MassTaskAction\TaskActions;

class MassAction
{

    public function dispatchActions($data)
    {
        switch ($data['entity']) {
            case 'Task':
                (new TaskActions())->dispatchActions($data);
            break;
        }

    }
}