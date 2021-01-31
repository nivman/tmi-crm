<?php

namespace App\Http\Controllers;

use App\Expense;
use App\Helpers\Filters;
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

    public function index(Request $request, $expensesId = -1, $entityType = null)
    {

        $ascending = $request->request->get('ascending') == 1 ? 'DESC' : 'ASC';
        $params = Filters::filters($request, 'expenses');

        $orderByExpensesValue = $request->query->get('orderBy');
        $hasRelation = (new Expense())->checkRelation($orderByExpensesValue);
        $sortByExpensesAttr = [$hasRelation, $orderByExpensesValue];
        if ($params['filter'] || $expensesId !== -1) {

            return (new Expense())->sortBy($ascending, $request, $params['params'], $sortByExpensesAttr);
        }
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

    public function getExpensesByProjectId(Request $request, $projectId)
    {


        $request->query->set('orderBy', 'project');
        $request->query->set('projectId', $projectId);
        $expenses = $this->index($request, $projectId, 'project_id');

      return  response()->json($expenses->getData());
    }
}
