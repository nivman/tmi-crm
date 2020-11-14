<?php

namespace App\Http\Controllers;

use App\SourcesOfArrival;
use Illuminate\Http\Request;

class SourcesOfArrivalController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

      return response()->json(SourcesOfArrival::vueTable(SourcesOfArrival::$columns));

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {

        return SourcesOfArrival::create($request->request->all());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\SourcesOfArrival  $sourcesOfArrival
     * @return \Illuminate\Http\Response
     */
    public function show(SourcesOfArrival $sourcesOfArrival)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\SourcesOfArrival  $sourcesOfArrival
     * @return \Illuminate\Http\Response
     */
    public function edit(SourcesOfArrival $sourcesOfArrival)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\SourcesOfArrival  $sourcesOfArrival
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, SourcesOfArrival $sourcesOfArrival)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\SourcesOfArrival  $sourcesOfArrival
     * @return \Illuminate\Http\Response
     */
    public function destroy(SourcesOfArrival $sourcesOfArrival)
    {
        //
    }
}
