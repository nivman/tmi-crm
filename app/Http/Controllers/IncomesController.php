<?php

namespace App\Http\Controllers;

use App\Income;
use App\Http\Requests\IncomeRequest;

class IncomesController extends Controller
{
    public function create()
    {
        $income = new Income;
        return $income->attributes();
    }

    public function destroy(Income $income)
    {
        $income->delete();
        return response(['success' => true], 204);
    }

    public function index()
    {
        return response()->json(Income::mine()->vueTable(Income::$columns));
    }

    public function show(Income $income)
    {
        $income->attributes = $income->attributes();
        $income->load($income->attributes->pluck('slug')->toArray());
        return $income;
    }

    public function store(IncomeRequest $request)
    {
        $v      = $request->validated();
        $income = $request->user()->incomes()->create($v);
        $income->categories()->sync($v['category']);
        return $income;
    }

    public function update(IncomeRequest $request, Income $income)
    {
        $v = $request->validated();
        $income->update($v);
        $income->categories()->sync($v['category']);
        return $income;
    }
}
