<?php

namespace App;

use App\Traits\LogActivity;
use App\Traits\Restrictable;
use App\Traits\VueTable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class EventsType extends Model
{
    use LogActivity, Restrictable, VueTable;

    public static $columns = ['name', 'color'];
    protected $fillable = ['name', 'color'];
    protected $hidden   = ['created_at', 'updated_at'];

    public function getEventsType()
    {
        return DB::table('events_types')->select('id', 'name')->get();
    }

    public function getEventsTypeById($id) {

        return DB::table('events_types')->select('id', 'name', 'color')
            ->where('id', $id)
            ->get();
    }

    public function getProjects()
    {

    }
}
