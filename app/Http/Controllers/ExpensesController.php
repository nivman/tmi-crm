<?php

namespace App\Http\Controllers;

use App\Expense;
use App\Http\Requests\ExpenseRequest;
 
use Illuminate\Http\Request;
class ExpensesController extends Controller
{
    public function create()
    {
        $expense = new Expense;
        return $expense->attributes();
    }

    public function destroy(Expense $expense)
    {
        $expense->delete();
        return response(['success' => true], 204);
    }

    public function index(Request $request)
    {
        return response()->json(Expense::vueTable(Expense::$columns));
    }

    public function show(Expense $expense)
    {
        $expense->attributes = $expense->attributes();
        $expense->load($expense->attributes->pluck('slug')->toArray());
        return $expense;
    }

    public function store(ExpenseRequest $request)
    {
        $v = $request->validated();

        $v['project_id'] = $request->request->get('project_id');
        $v['vendor_id'] = $request->request->get('vendor') ? $request->request->get('vendor')['id'] : null;
        $v['category_id'] = $request->request->get('category_id');
        $expense = $request->user()->expenses()->create($v);
        $expense->categories()->sync($v['category']);
        return $expense;
    }

    public function update(ExpenseRequest $request, Expense $expense)
    {
        $v = $request->validated();
        $v['project_id'] = $request->request->get('project_id');
        $v['vendor_id'] = $request->request->get('vendor') ? $request->request->get('vendor')['id'] : null;
        $expense->update($v);
        $expense->categories()->sync($v['category']);
        return $expense;
    }

    public function getExpensesByProjectId($projectId)
    {
      return  response()->json(Expense::where(['project_id' => $projectId])->mine()->vueTable(Expense::$columns));
    }
}
