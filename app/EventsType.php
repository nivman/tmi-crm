<?php

namespace App;

use App\Traits\LogActivity;
use App\Traits\Restrictable;
use App\Traits\VueTable;
use Illuminate\Database\Eloquent\Model;

class EventsType extends Model
{
    use LogActivity, Restrictable, VueTable;

    public static $columns = ['name', 'color'];
    protected $fillable = ['name', 'color'];
    protected $hidden   = ['created_at', 'updated_at'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
