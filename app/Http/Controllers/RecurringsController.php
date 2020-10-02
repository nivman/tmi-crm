<?php

namespace App\Http\Controllers;

use App\Recurring;
use App\Helpers\Order;
use App\Http\Requests\RecurringRequest;

class RecurringsController extends Controller
{
    public function create()
    {
        $recurring = new Recurring;
        return $recurring->attributes();
    }

    public function destroy(Recurring $recurring)
    {
        $recurring->delete();
        return response(['success' => true], 204);
    }

    public function index()
    {
        return response()->json(Recurring::myInvoices()->vueTable(Recurring::$columns));
    }

    public function show(Recurring $recurring)
    {
        $recurring->load('items', 'taxes');
        $recurring->attributes = $recurring->attributes();
        $recurring->load($recurring->attributes->pluck('slug')->toArray());
        return $recurring;
    }

    public function store(RecurringRequest $request)
    {
        $v = Order::prepareData($request->validated(), $request, true);
        return Order::orderTransaction($v, 'App\Recurring');
    }

    public function update(RecurringRequest $request, Recurring $recurring)
    {
        $v = Order::prepareData($request->validated(), $request);
        return Order::orderTransaction($v, 'App\Recurring', $recurring);
    }
}
