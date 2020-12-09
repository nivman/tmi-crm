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

    public static $columns = ['title', 'details', 'start_date', 'end_date', 'color', 'user_id', 'project_id','type_id','contact.first_name'];
    protected $fillable = ['title', 'details', 'start_date', 'end_date', 'color', 'user_id','type_id', 'contact_id' ,'project_id', 'type', 'contact_first_name'];
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

    public function checkRelation($key)
    {
        return in_array($key, Event::$columns);

    }


    public function sortBy($ascending, $request, $params, $sortByTaskAttr = [], $filterByEntity =[])
    {

        $query = Event::query();
        $result = null;

        $request->query->set('orderBy', $params[0]['orderByValue']);

        foreach ($params as $key => $param) {

            $result = $query->leftJoin($params[$key]['tableToJoin'], $params[$key]['orderBy'], '=', $params[$key]['columnToJoin'])
                ->select('events.*')
                ->where(function ($q) use ($params, $key, $filterByEntity) {

                    if (count($filterByEntity) > 0) {

                        $q->where('events.'.$filterByEntity['entityType'], '=', $filterByEntity['entityId']);

                        if ($params[0]['tableToJoin'] === 'events_types' ) {
                            $q->OrWhere($params[$key]['orderByValue'], 'LIKE', "%{$params[$key]['query']}%")->where($params[$key]['orderBy'], '=', null);

                        }else {
                            $q->where($params[$key]['orderByValue'], 'LIKE', "%{$params[$key]['query']}%") ;
                        }
                    }else{

                        $q->where($params[$key]['orderByValue'], 'LIKE', "%{$params[$key]['query']}%");

                        if ($params[$key]['query'] == '') {

                            $q->orWhere($params[$key]['orderByValue'], '=', null);
                        }
                    }
                })
                ->with(['type', 'contact', 'project'])
                ->mine()
                ->orderBy($params[$key]['orderByValue'], $ascending)
                ->vueTable(Event::$columns, false, 'events');

        }

        $events['data'] = $query->get()->toArray();
        $events['count'] = $result['count'];
        return response()->json($events);
    }
}
