<?php

namespace App;

use App\Traits\VueTable;
use Spatie\Activitylog\Models\Activity as ActivityModel;

class Activity extends ActivityModel
{
    use VueTable;

    public static $columns = ['id', 'created_at', 'description', 'subject_id', 'subject_type', 'causer_id', 'causer_type'];

    protected $with = ['user'];

    public function user()
    {
        return $this->belongsTo(User::class, 'causer_id');
    }
}
