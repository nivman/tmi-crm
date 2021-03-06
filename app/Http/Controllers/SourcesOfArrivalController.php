<?php

namespace App\Http\Controllers;

use App\ArrivalSources;
use App\Http\Requests\SourcesOfArrivalRequest;
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


      return response()->json(ArrivalSources::vueTable(ArrivalSources::$columns));

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {

        return ArrivalSources::create($request->request->all());
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
     * @param  \App\ArrivalSources  $sourcesOfArrival
     * @return \Illuminate\Http\Response
     */
    public function show(ArrivalSources $sourcesOfArrival)
    {

        return response($sourcesOfArrival);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\ArrivalSources  $sourcesOfArrival
     * @return \Illuminate\Http\Response
     */
    public function edit(ArrivalSources $sourcesOfArrival)
    {
        return response($sourcesOfArrival);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\ArrivalSources  $sourcesOfArrival
     * @return \Illuminate\Http\Response
     */
    public function update(SourcesOfArrivalRequest $request, ArrivalSources $sourcesOfArrival)
    {
        $v = $request->validated();
        $sourcesOfArrival->fill($v)->save();
        return $sourcesOfArrival;
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
