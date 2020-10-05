<?php

namespace App\Http\Controllers;

use App\Account;
use App\Transaction;
use Illuminate\Http\Request;
use App\Http\Requests\AccountRequest;

class AccountsController extends Controller
{
    public function create()
    {
        $account = new Account;
        return $account->attributes();
    }

    public function destroy(Account $account)
    {
        if ($account->expenses()->exists()) {
            return response(['message' => 'Account has been attached to some expenses and can not be deleted.'], 422);
        } elseif ($account->incomes()->exists()) {
            return response(['message' => 'Account has been attached to some incomes and can not be deleted.'], 422);
        } elseif ($account->toTransfers()->exists()) {
            return response(['message' => 'Account has been attached to some transfers and can not be deleted.'], 422);
        } elseif ($account->fromTransfers()->exists()) {
            return response(['message' => 'Account has been attached to some transfers and can not be deleted.'], 422);
        }

        if (demo()) {
            return response(['message' => 'This feature is disabled on demo.'], 422);
        }

        $account->journal->transactions()->delete();
        $account->journal->delete();
        $account->delete();
        return response(['success' => true], 204);
    }

    public function index(Request $request)
    {
        if ($request->all) {
            return Account::select(['id', 'name', 'id as value'])->orderBy('name', 'asc')->get();
        }
        if (!auth()->user()->hasRole('admin')) {
            abort(403, 'Access denied!');
        }
        return response()->json(Account::with('journal')->vueTable(Account::$columns));
    }

    public function show(Account $account)
    {
        $account->value = $account->id;
        $account->attributes = $account->attributes();

        $account->offline = $account->offline === 1 ? true : false;
        $account->load($account->attributes->pluck('slug')->toArray());
        return $account;
    }

    public function store(AccountRequest $request)
    {
        $v = $request->validated();
        return Account::create($v);
    }

    public function transactions(Account $account)
    {
        return response()->json(Transaction::whereHas('journal', function ($query) use ($account) {
            $query->where('morphed_id', $account->id);
        })->with('journal.morphed')->vueTable(Transaction::$columns));
    }

    public function update(AccountRequest $request, Account $account)
    {

        $v = $request->validated();

        //dd($v);
        $account->update($v);
        return $account;
    }
}
