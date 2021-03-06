<?php

namespace App\Http\Controllers;

use App\Contact;
use App\Event;
use App\EventsType;
use App\File;
use App\Helpers\Date;
use App\Helpers\Filters;
use App\Task;
use App\TasksEventsRepeat;
use Carbon\Carbon;
use Doctrine\DBAL\Events;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EventsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    protected function isValid($request)
    {
        return $request->validate([
            'title'      => 'required|max:255|string|not_regex:/<[^>]*>/',
            'start_date' => 'required|date_format:Y-m-d H:i',
            'end_date'   => 'nullable|date_format:Y-m-d H:i|after_or_equal:start_date',
            'color'      => 'nullable|string',
            'details'    => 'nullable|not_regex:/<[^>]*>/',
            'contact_id' => 'required|integer',
            'type_id'    => 'required|integer',
            'project_id' => 'nullable|integer'
        ]);
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Event  $event
     * @return \Illuminate\Http\Response
     */
    public function destroy(Event $event)
    {

        $fileArr = (new File)->getFileByEventId($event->id);
        if (count($fileArr)> 0) {
             $fileObject = File::find($fileArr[0]->id);
             $fileObject->update(['event_id' => null]);
        }

        $event->delete();
        return response(['success' => true], 204);
    }

    public function index(Request $request)
    {
        $start = $request->start == null ? '' : $request->start;
        $end = !$request->end ? '' : $request->end;
        $fetchData = $request->fetchData;

        $events = $fetchData === '0' ? [] : Event::whereBetween('start_date',[$start,$end])->with('type')->get();
        $tasks = $fetchData === '1' ? new Collection() :
            Task::whereBetween('start_date',[$start,$end])
            ->where('repeat', 0)
            ->with('status', 'customer')->get();

        $repeatTasksEvents = $fetchData === '1' ? new Collection() :
            TasksEventsRepeat::whereBetween('start_date',[$start,$end])
            ->where('show', 1)
            ->with('status' ,'task')->get();

        $taskTitles = array_map(function($task) {
            $task['title'] = $task['name'];
            return $task;
        }, $tasks->toArray() );

        $repeatTasksEventsTitles = array_map(function($repeatTasksEvent) {
            $repeatTasksEvent['title'] = $repeatTasksEvent['name'];
            $repeatTasksEvent['repeat'] = true;

            return $repeatTasksEvent;
        }, $repeatTasksEvents->toArray() );

        return ['tasks' => $taskTitles, 'events' => $events, 'repeatTasksEventsTitles' =>$repeatTasksEventsTitles];
    }

    public function list(Request $request)
    {
        $ascending = $request->request->get('ascending') == 1 ? 'DESC' : 'ASC';

        $params = Filters::filters($request, 'events');
        $orderByEventValue = $request->query->get('orderBy');
        $hasRelation = (new Event())->checkRelation($orderByEventValue);

        $sortByEventAttr = [$hasRelation, $orderByEventValue];

        if ($params['filter']) {

            return (new Event())->sortBy($ascending, $request, $params['params'], $sortByEventAttr);
        }
        return  Event::with(['type', 'contact', 'project'])->mine()->vueTable(Event::$columns);


        if($request->date == null) {
            $ascending = $request->request->get('ascending') == 1 ? 'DESC' : 'ASC';
            $requestOrderBy = $request->query->get('orderBy');
            $orderBy = $requestOrderBy === 'type' ? 'type_id' : $requestOrderBy;
            $request->query->set('orderBy', $orderBy);

            return response()->json(Event::orderBy($orderBy,$ascending)->with(['type', 'contact', 'project'])->mine()->vueTable(Event::$columns));

        }

        list($year, $month) = explode('-',  $request->date);

        return response()->json(Event::whereYear('start_date', $year)->whereMonth('start_date', $month)->mine()->vueTable(Event::$columns));
    }

    public function create()
    {
        $eventsType = (new EventsType())->getEventsType();
       // $projects = (new EventsType())->getProjects();
        return $eventsType;

    }
    
    public function show(Event $event)
    {

        $event->contact = $event->contact()->get();
        $event->type = $event->type()->get();
        return $event;
    }

    public function store(Request $request)
    {
        $date = Date::formatDateTime($request);
        $request->request->set('start_date', $date['start_date']);
        $request->request->set('end_date', $date['end_date']);
        $v['contact_id'] = isset($request->request->get('contact')['id']) ? $request->request->get('contact')['id'] : $request->request->get('contact_id');
        $v['project_id'] = isset($request->request->get('project')['id']) ? $request->request->get('project')['id'] : $request->request->get('project_id');
        $v['type_id'] = isset($request->request->get('type')['id']) ? isset($request->request->get('type')['id']) :$request->request->get('type_id');
        $request->request->set('type_id', $v['type_id']);
        $request->request->set('contact_id', $v['contact_id']);
        $request->request->set('project_id', $v['project_id']);

        $v = $this->isValid($request);



        return $request->user()->events()->create($v);
    }

    public function update(Request $request, Event $event)
    {
        $date = Date::formatDateTime($request);
        if($event->contact_id == 5 && $event->title === 'הקלטה') {
            (new File)->moveFiles($request, $event);

        }


        $request->request->set('start_date', $date['start_date']);
        $request->request->set('end_date', $date['end_date']);

        $v['type_id'] = isset($request->request->get('type')['id']) ? $request->request->get('type')['id'] :$request->request->get('type_id');
        $v['contact_id'] = isset($request->request->get('contact')['id']) ? $request->request->get('contact')['id']: $request->request->get('contact_id');
        $v['project_id'] = isset($request->request->get('project')['id']) ? $request->request->get('project')['id']: $request->request->get('project_id');
        $request->request->set('type_id', $v['type_id']);
        $request->request->set('contact_id', $v['contact_id']);
        $request->request->set('project_id', $v['project_id']);
        $v = $this->isValid($request);

        $event->update($v);
        return $event;
    }

    public function getProjectEvents(Request $request ,$projectId)
    {
        return $this->filterBy($request, [$projectId], 'project_id');
    }

    public function getCustomersEvents(Request $request ,$customerId)
    {
        $contacts = (new Contact)->getContactByCustomer($customerId);
        $contactsIds = array_column($contacts->toArray(), 'id');
        return $this->filterBy($request, $contactsIds, 'contact_id');
    }

    public function updateCalendarDates(Request $request)
    {

        $eventId = $request->request->get('event')['event']['id'];
        $request->request->set('start_date', $request->request->get('start'));
        $request->request->set('end_date', $request->request->get('end'));
        $v =['start_date' => $request->request->get('start'), 'end_date' => $request->request->get('end')];
        $event = Event::find($eventId);
        $event->update($v);
        return $event;
    }

    private function filterBy($request, $entityId, $entityType)
    {
        Filters::filters($request, 'events');
        $events = Event::whereIn($entityType, $entityId)->with(['contact',  'project'])->mine()->vueTable(Event::$columns);
        return response()->json($events)->original;
    }

}
