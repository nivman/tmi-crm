<?php

namespace App\Http\Controllers;

use App\StatusHistory;
use Illuminate\Http\Request;

class StatusHistoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    public function get($entity_type, $entity_id)
    {
        $type = '';
        switch ($entity_type) {
            case 'customer':
                $type = 'App\Customer';
            break;
        }

        return (new StatusHistory())->getEntityStatusesHistory($type, $entity_id);

    }
    /**
     * Display the specified resource.
     *
     * @param  \App\StatusHistory  $statusHistory
     * @return \Illuminate\Http\Response
     */
    public function show(StatusHistory $statusHistory)
    {

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\StatusHistory  $statusHistory
     * @return \Illuminate\Http\Response
     */
    public function edit(StatusHistory $statusHistory)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\StatusHistory  $statusHistory
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, StatusHistory $statusHistory)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\StatusHistory  $statusHistory
     * @return \Illuminate\Http\Response
     */
    public function destroy(StatusHistory $statusHistory)
    {
        //
    }
}
