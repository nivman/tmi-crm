<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class TasksEventsRepeatRule extends Model
{

    public static $columns = ['id', 'rules', 'details', 'task_id', 'task_id', 'event_id', 'created_at'];
    protected $fillable = ['id', 'rules', 'details', 'task_id', 'task_id', 'event_id', 'created_at'];
    protected $hidden = ['created_at', 'updated_at'];

    public function setRule($entityCreated, $request)
    {

       $data = [
           'rules' =>json_encode($request->get('recurrenceRule')),
           'task_id' => $entityCreated->id,
           'text_rule' => $request->get('recurrenceText')
           ];

       return DB::table('tasks_events_repeat_rules')->insert($data);

    }

    public function status()
    {
        return $this->belongsTo(Status::class);
    }

    public function getFraq($fraq)
    {
        switch ($fraq) {
            case 1:
                return 'MONTHLY';

            case 2:
                return 'WEEKLY';
        }
    }

    public function getDays($days)
    {
        $dayMaps = [0 => 'MO', 1 => 'TU', 2 => 'WE', 3 => 'TH', 4 => 'FR', 5 => 'SA', 6 => 'SU'];

        return array_map(function ($a) use ($dayMaps)
        {
               return $dayMaps[$a];

        }, $days);

    }

    public function getFirstReapedTask($taskId)
    {
        $task =  DB::table('tasks_events_repeats')->select('*')
            ->where('task_id', $taskId)
            ->orderBy('start_date')
            ->limit(1)
            ->get()->toArray();

        return ['start' => $task[0]->start_date, 'end' => $task[0]->end_date];
    }

    public function getAllRepeatedTasks($taskId) {
        $tasks =  DB::table('tasks_events_repeats')->select('*')
            ->where('task_id', $taskId)
            ->orderBy('id')
            ->get()->toArray();

        return Task::hydrate($tasks);
    }
}