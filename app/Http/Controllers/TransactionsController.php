<?php

namespace App\Http\Controllers;

use App\Vendor;
use App\Account;
use App\Customer;
use App\Transaction;

class TransactionsController extends Controller
{
    public function account(Account $account)
    {
        return response()->json(Transaction::whereHas('journal', function ($query) use ($account) {
            $query->where('morphed_id', $account->id)->where('morphed_type', 'App\Account');
        })->with('journal.morphed')->vueTable(Transaction::$columns));
    }

    public function customer(Customer $customer)
    {
        return response()->json(Transaction::whereHas('journal', function ($query) use ($customer) {
            $query->where('morphed_id', $customer->id)->where('morphed_type', 'App\Customer');
        })->with('journal.morphed')->vueTable(Transaction::$columns));
    }

    public function index()
    {
        return response()->json(Transaction::with('journal.morphed')->vueTable(Transaction::$columns));
    }

    public function vendor(Vendor $vendor)
    {
        return response()->json(Transaction::whereHas('journal', function ($query) use ($vendor) {
            $query->where('morphed_id', $vendor->id)->where('morphed_type', 'App\Vendor');
        })->with('journal.morphed')->vueTable(Transaction::$columns));
    }
}
