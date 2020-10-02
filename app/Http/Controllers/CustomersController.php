<?php

namespace App\Http\Controllers;

use App\Customer;
use Illuminate\Http\Request;
use App\Http\Requests\CustomerRequest;

class CustomersController extends Controller
{
    public function create()
    {
        $customer = new Customer;
        return $customer->attributes();
    }

    public function destroy(Customer $customer)
    {
        if ($customer->invoices()->exists()) {
            return response(['message' => 'Customer has been attached to some invoices and can not be deleted.'], 422);
        } elseif ($customer->payments()->exists()) {
            return response(['message' => 'Customer has been attached to some payments and can not be deleted.'], 422);
        } elseif ($customer->users()->exists()) {
            return response(['message' => 'Customer has been attached to some users and can not be deleted.'], 422);
        }
        $customer->delete();
        return response(['success' => true], 204);
    }

    public function index()
    {
        return response()->json(Customer::with('journal')->mine()->vueTable(Customer::$columns));
    }

    public function search(Request $request)
    {
        $v       = $request->validate(['query' => 'required|string']);
        $results = Customer::search($v['query'])->select(
            \DB::raw("*, id as value,
                if (`name` LIKE '{$v['query']}%', 20, if (`name` LIKE '%{$v['query']}%', 10, 0))
                + if (`company` LIKE '%{$v['query']}%', 5,  0)
                + if (`phone` LIKE '%{$v['query']}%', 4,  0)
                + if (`email` LIKE '%{$v['query']}%', 3,  0)
                as weight")
        )->orderBy('weight', 'desc')->limit(10)->get();
        return $results;
    }

    public function show(Customer $customer)
    {
        $customer->attributes = $customer->attributes();
        $customer->load($customer->attributes->pluck('slug')->toArray());
        return $customer;
    }

    public function store(CustomerRequest $request)
    {
        $v            = $request->validated();
        $v['user_id'] = auth()->id();
        return Customer::create($v);
    }

    public function update(CustomerRequest $request, Customer $customer)
    {
        $user = auth()->user();
        if ($user->hasRole(['staff', 'customer', 'vendor']) && $user->customer_id != $customer->id) {
            abort(403);
        }
        $v = $request->validated();
        $customer->update($v);
        return $customer;
    }
}
