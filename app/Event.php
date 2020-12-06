<?php

namespace App;

use App\Traits\LogActivity;
use App\Traits\Restrictable;
use App\Traits\VueTable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Event extends Model
{
    use LogActivity, Restrictable, VueTable;

    public static $columns = ['title', 'details', 'start_date', 'end_date', 'color', 'user_id', 'project_id','type_id'];
    protected $fillable = ['title', 'details', 'start_date', 'end_date', 'color', 'user_id','type_id', 'contact_id' ,'project_id'];
    protected $hidden   = ['updated_at'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function contact()
    {
        return $this->belongsTo(Contact::class);
    }

    public function type()
    {
        return $this->belongsTo(EventsType::class);
    }

    public function getType($typeId)
    {
        return (new EventsType())->getStatusById($typeId);
    }

    public function getEventsByContacts($contactssIds)
    {
        $events = DB::table('events', )
            ->select('*')
            ->whereIn('contact_id', $contactssIds)
            ->get()->toArray();

        return Event::hydrate($events);;
    }
}
