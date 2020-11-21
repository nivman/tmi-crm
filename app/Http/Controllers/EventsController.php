<?php

namespace App\Http\Controllers;

use App\Event;
use App\EventsType;
use App\Helpers\Date;
use App\Helpers\Filters;
use App\Task;
use Carbon\Carbon;
use Doctrine\DBAL\Events;
use Illuminate\Http\Request;

class EventsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    protected function isValid($request)
    {
        return $request->validate([
            'title'      => 'required|max:55|string|not_regex:/<[^>]*>/',
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

        $event->delete();
        return response(['success' => true], 204);
    }

    public function index(Request $request)
    {
        $start = $request->start == null ? '' : $request->start;
        $end = !$request->end ? '' : $request->end;
        $events = Event::whereBetween('start_date',[$start,$end])->with('type')->get();
        $tasks = Task::whereBetween('start_date',[$start,$end])->with('status')->get();

        $taskTitles = array_map(function($task) {
            $task['title'] = $task['name'];
            return $task;
        }, $tasks->toArray() );
        return ['tasks' => $taskTitles, 'events' => $events];
    }

    public function list(Request $request)
    {

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
        return $this->filterBy($request, $projectId, 'project_id');
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
        $events = Event::where($entityType, $entityId)->with(['contact',  'project'])->mine()->vueTable(Event::$columns);
        return response()->json($events)->original;
    }

}
