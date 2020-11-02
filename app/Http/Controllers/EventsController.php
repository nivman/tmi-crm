<?php

namespace App\Http\Controllers;

use App\Event;
use App\EventsType;
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
            'title'      => 'required|max:55',
            'start_date' => 'required|date_format:Y-m-d H:i',
            'end_date'   => 'nullable|date_format:Y-m-d H:i|after_or_equal:start_date',
            'color'      => 'nullable',
            'details'    => 'nullable',
            'contact_id' => 'required',
            'type_id'    => 'required',
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
        $date = $request->date == null ? '2020-10' : $request->date;

        list($year, $month) = explode('-', $date);

        return response()->json(Event::whereYear('start_date', $year)->whereMonth('start_date', $month)->mine()->orderBy('start_date')->get());
    }

    public function list(Request $request)
    {

        if($request->date == null) {
            $ascending = $request->request->get('ascending') == 1 ? 'DESC' : 'ASC';
            $requestOrderBy = $request->query->get('orderBy');
            $orderBy = $requestOrderBy === 'type' ? 'type_id' : $requestOrderBy;
            $request->query->set('orderBy', $orderBy);

            return response()->json(Event::orderBy($orderBy,$ascending)->with(['type', 'contact'])->mine()->vueTable(Event::$columns));

        }

        list($year, $month) = explode('-',  $request->date);

        return response()->json(Event::whereYear('start_date', $year)->whereMonth('start_date', $month)->mine()->vueTable(Event::$columns));
    }

    public function eventsTypes()
    {
        $customerStatuses = (new EventsType())->getEventsType();

        return $customerStatuses;

    }
    
    public function show(Event $event)
    {
        return $event;
    }

    public function store(Request $request)
    {
        //TODO move all the date handling
        $start_date =  $request->request->get('start_date');
        $end_date =  $request->request->get('end_date');

        $start = Carbon::createFromFormat('d/m/Y H:i', $start_date)->format('Y-m-d H:i');
        $end = Carbon::createFromFormat('d/m/Y H:i', $end_date)->format('Y-m-d H:i');

        $request->request->set('start_date', $start);
        $request->request->set('end_date', $end);
        $v['contact_id'] = isset($request->request->get('contact')['id']) ? $request->request->get('contact')['id']: $request->request->get('contact_id');
        $v['type_id'] = $request->request->get('type')['id'];
        $request->request->set('type_id', $v['type_id']);
        $request->request->set('contact_id', $v['contact_id']);
        $v = $this->isValid($request);



        return $request->user()->events()->create($v);
    }

    public function update(Request $request, Event $event)
    {
        $start_date =  $request->request->get('start_date');

        $end_date =  $request->request->get('end_date');

        $start = Carbon::createFromFormat('Y-m-d H:i:s', $start_date)->format('Y-m-d H:i');
        $end = Carbon::createFromFormat('Y-m-d H:i:s', $end_date)->format('Y-m-d H:i');

        $request->request->set('start_date', $start);
        $request->request->set('end_date', $end);

        $v['type_id'] = $request->request->get('type')['id'];
        $v['contact_id'] = isset($request->request->get('contact')['id']) ? $request->request->get('contact')['id']: $request->request->get('contact_id');
        $request->request->set('type_id', $v['type_id']);
        $request->request->set('contact_id', $v['contact_id']);
        $v = $this->isValid($request);
        $event->update($v);
        return $event;
    }
}
