<?php

namespace App\Events;

use App\Status;
use Illuminate\Queue\SerializesModels;

class StatusChangeEvent
{
    use SerializesModels;

    public Status $status;
    public $entity_id;
    /**
     * StatusChange constructor.
     * @param Status $status

     */
    public function __construct(Status $status)
    {
        $this->entity_id = $status->getAttribute('entity_id');
        $this->status = $status;
    }
}
