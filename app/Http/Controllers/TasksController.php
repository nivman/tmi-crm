<?php

namespace App\Http\Controllers;

use App\Category;
use App\Customer;
use App\Helpers\Date;
use App\Http\Requests\TaskRequest;
use App\Status;
use App\Task;
use App\TaskPriority;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Class TasksController
 * @package App\Http\Controllers
 */
class TasksController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(Request $request)
    {

        if($request->query->get('byColumn')) {
            $ascending = $request->request->get('ascending') == 1 ? 'DESC' : 'ASC';
            $requestOrderBy = $request->query->get('orderBy');
            $orderBy = $requestOrderBy === 'priority' ? 'priority_id' : $requestOrderBy;
            $request->query->set('orderBy', $orderBy);

            return response()->json(Task::orderBy($orderBy, $ascending)->with(['customer', 'priority', 'status','category'])->mine()->vueTable(Task::$columns));
        }
        return response()->json(Task::with(['customer', 'priority', 'status','category'])->mine()->vueTable(Task::$columns));
    }

    public function create()
    {
        $taskStatuses = (new Status)->getAllEntityStatus('App\Task');
        $priorities = (new TaskPriority)->getAllTaskPriority()->toArray();
        $taskCategories =   (new Category())->getAllEntityCategories('App\Task');

        $task = new Task;
        return [
            'attributes' => $task->attributes(),
            'statuses' => $taskStatuses,
            'priorities' => $priorities,
            'categories' => $taskCategories
            ];
    }

    /**
     * @param TaskRequest $request
     * @return Task
     */
    public function store(TaskRequest $request)
    {

        $v = $request->validated();
        $v['customer_id'] = $request->request->get('customer') ? $request->request->get('customer')['id'] : null;
        $v['priority_id'] = $request->request->get('priority') ? $request->request->get('priority')['id'] : null;
        $v['category_id'] = $request->request->get('category') ? $request->request->get('category')['id'] : null;
        $v['status_id'] = $request->request->get('status') ? $request->request->get('status')['id'] : null;
        $task =  new Task();
        Task::create($v);

        return $task;
    }

    /**
     * @param Task $task
     * @return array
     */
    public function show(Task $task)
    {

        $task->attributes = $task->attributes();
        $task->status = $task->getStatus($task->getAttribute('status_id'));
        $task->priority = $task->getPriority($task->getAttribute('priority_id'));
        $task->customer = $task->getCustomer($task->getAttribute('customer_id'));

        $tasksStatuses = (new Status)->getAllEntityStatus('App\Task');
        $priorities = (new TaskPriority)->getAllTaskPriority()->toArray();
        $task->load($task->attributes->pluck('slug')->toArray());

        return [
            'task' => $task,
            'status' => $task->status,
            'priority' =>$task->priority,
            'customer' => $task->customer,
            'statuses' => $tasksStatuses,
            'priorities' => $priorities
        ];
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function edit(Task $task)
    {
        //
    }


    /**
     * @param TaskRequest $request
     * @param Task $task
     * @return Task
     */
    public function update(TaskRequest $request, Task $task)
    {
        $v = $request->validated();
        $v['status_id'] = $request->request->get('status') ? $request->request->get('status')['id'] : null;
        $v['priority_id'] = $request->request->get('priority') ? $request->request->get('priority')[0]['id'] : null;
        $v['customer_id'] = $request->request->get('customer') ? $request->request->get('customer')['id'] : null;
        $task->update($v);
        return $task;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function destroy(Task $task)
    {
        $task->delete();
        return response(['success' => true], 204);
    }

    public function details(TaskRequest $request, $term, $id)
    {
        $v = $request->validated();
        $v['details'] = $term;
        $task = Task::find($id);
        $task->update($v);
        return $task;
    }

    public function getCustomer($id)
    {
       return Customer::find($id);
    }

    public function getCustomerTasks(Request $request ,$customerId)
    {
        if($request->query->get('byColumn')) {
            $ascending = $request->request->get('ascending') == 1 ? 'DESC' : 'ASC';
            $requestOrderBy = $request->query->get('orderBy');
            $orderBy = $requestOrderBy === 'priority' ? 'priority_id' : $requestOrderBy;
            $request->query->set('orderBy', $orderBy);

            return response()->json(Task::where('customer_id', $customerId)->orderBy($orderBy, $ascending)->with(['customer', 'priority', 'status'])->mine()->vueTable(Task::$columns));
        }
       return response()->json(Task::where('customer_id', $customerId)->with(['customer', 'priority', 'status'])->mine()->vueTable(Task::$columns));
    }
}
