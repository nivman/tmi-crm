<?php


namespace App\Http\Controllers;


use App\EventsType;

class EventsTypesController extends Controller
{

    public function getEventsTypes()
    {
        return (new EventsType())->getEventsType();
    }
}