<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class TasksEventsRepeat extends Model
{

    public static $columns = ['id', 'name', 'details', 'start_date', 'end_date', 'status_id', 'task_id', 'event_id'];
    protected $fillable = ['id', 'name', 'details', 'start_date', 'end_date', 'status_id', 'task_id', 'event_id','created_at', 'show'];
    protected $hidden = ['created_at', 'updated_at'];

    public function setRecurrence($entityCreated, $request, $type)
    {
        $data = [];
        $endTimeRecurrence = $this->setEndTimeRecurrence($request->get('recurrence'), $entityCreated->end_date);


        foreach ($request->get('recurrence') as $key =>$recurrence) {

            $data[$key] =[
                'name' => $entityCreated->name,
                'details' =>  $entityCreated->details,
                'entity_type' => $type,
                'status_id' => $entityCreated->status_id,
                'start_date' => Carbon::parse($recurrence),
                'end_date' => $endTimeRecurrence[$key]
                ];
            if($type === 'App\Task') {
                $data[$key]['task_id'] = $entityCreated->id;

            }

        }

       return DB::table('tasks_events_repeats')->insert($data);

    }

    public function status()
    {
        return $this->belongsTo(Status::class);
    }

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    private function setEndTimeRecurrence($recurrence, $endTime)
    {
        $endRecurrence = [];
        $end =  Carbon::createFromFormat('Y-m-d H:i', $endTime) ;

        foreach ($recurrence as $item) {
            $endItem =  Carbon::parse($item) ;

            $endItem->setHour($end->hour);
            $endItem->setMinutes($end->minute);

            $endRecurrence[] = $endItem->toDateTimeString();
        }
        return $endRecurrence;

    }
}