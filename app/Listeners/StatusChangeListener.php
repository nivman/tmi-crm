<?php

namespace App\Listeners;

use App\Events\StatusChangeEvent;
use App\Status;

use App\StatusHistory;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\DB;

class StatusChangeListener implements ShouldQueue
{
    const NO_STATUS = -1;
    public function handle(StatusChangeEvent $event)
    {
        $status = $event->status;
        $entity_id = $event->entity_id;
        $statusChange = new StatusHistory;

        $where = ['entity_id' => $entity_id, 'entity_type' => $status->entity_name];
        $results = StatusHistory::where($where)->select('status_id')->latest()->first();
        $currentStatus = $results ? $results->toArray()['status_id'] : StatusChangeListener::NO_STATUS;
        if ($currentStatus != $status->id) {
            $statusChange::create([
                'entity_type' => $status->entity_name,
                'entity_id' => $entity_id,
                'status_id' => $status->id,
            ]);
        }
    }
}
