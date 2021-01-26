<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpSaleRequest;
use App\UpSale;
use Illuminate\Http\Request;

class UpSaleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        return response()->json(UpSale::with(['category'])->mine()->vueTable(UpSale::$columns));
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
    public function store(UpSaleRequest $request)
    {

        $v = $request->validated();

        $v['project_id'] = $request->request->get('project_id');
        $v['category_id'] = $request->request->get('category_id');

        return UpSale::create($v);

    }

    /**
     * Display the specified resource.

     * @return \Illuminate\Http\Response
     */
    public function show($upSaleId)
    {
        return UpSale::find($upSaleId);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\UpSale  $upSale
     * @return \Illuminate\Http\Response
     */
    public function edit(UpSale $upSale)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\UpSale  $upSale
     * @return \Illuminate\Http\Response
     */
    public function update(UpSaleRequest $request, $upSaleId)
    {
        $upSale = UpSale::find($upSaleId);
        $v = $request->validated();

        $v['project_id'] = $request->request->get('project_id');
        $v['category_id'] = $request->request->get('category_id');
        $upSale->update($v);

        return $upSale;

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\UpSale  $upSale
     * @return \Illuminate\Http\Response
     */
    public function destroy(UpSale $upSale)
    {
        //
    }

    public function getUpSalesByProjectId($projectId)
    {
        $upSales = UpSale::where(['project_id' => $projectId])->mine()->vueTable(UpSale::$columns);

        $sum = array_sum(array_column($upSales['data'],'amount'));
        $upSales['sum'] = $sum;

        return  response()->json($upSales);
    }
}
