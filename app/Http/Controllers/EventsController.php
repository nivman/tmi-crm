<?php

namespace App\Http\Controllers;

use App\Event;
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
            'start_date' => 'required|date_format:Y-m-d',
            'end_date'   => 'nullable|date_format:Y-m-d|after_or_equal:start_date',
            'color'      => 'nullable',
            'details'    => 'nullable',
            'contact_id' => 'required'
        ]);
    }

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
        $date = $request->date == null ? '2020-10' : $request->date;

        list($year, $month) = explode('-', $date);

        return response()->json(Event::whereYear('start_date', $year)->whereMonth('start_date', $month)->mine()->vueTable(Event::$columns));
    }

    public function show(Event $event)
    {
        return $event;
    }

    public function store(Request $request)
    {

        $v = $this->isValid($request);

        $v['contact_id'] = $request->request->get('contact_id');

        return $request->user()->events()->create($v);
    }

    public function update(Request $request, Event $event)
    {
        $v = $this->isValid($request);
        $event->update($v);
        return $event;
    }
}
