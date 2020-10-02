<?php

namespace App\Traits;

use Spatie\Activitylog\Traits\LogsActivity;

trait LogActivity
{
    use LogsActivity;

    protected static $logFillable = true;
    // protected static $logOnlyDirty = true;
    // protected static $recordEvents = ['created', 'updated', 'deleting'];

    protected static function getLogNameToUse()
    {
        return (new \ReflectionClass(static::class))->getShortName();
    }

    public function getDescriptionForEvent($event)
    {
        return static::getLogNameToUse() . " has been {$event}";
    }
}
