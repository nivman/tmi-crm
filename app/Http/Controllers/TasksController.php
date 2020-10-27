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
use Illuminate\Support\Facades\DB;

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
        $ascending = $request->request->get('ascending') == 1 ? 'DESC' : 'ASC';

        if ($request->query->get('query') == '{}') {
            $request->query->set('query', '');
        }
        $params = [];
        $query = '';
        $filter = false;
        if(json_decode($request->query->get('query'))){
            $query = json_decode($request->query->get('query'));
        }

        if($request->query->get('orderBy') === 'priority') {

            $filter = true;
            $params[] = [
               'tableToJoin' => 'task_priorities' ,
               'orderBy' => 'tasks.priority_id',
               'orderByValue' => 'task_priorities.id',
               'columnToJoin' => 'task_priorities.id',
               'query' => ''];

        }

        if($request->query->get('orderBy') === 'customer' || isset($query->customer)) {
           $filter = true;
           $customerName = isset($query->customer) ? $query->customer : '';
           $params[] = [
                'tableToJoin' => 'customers' ,
                'orderBy' => 'tasks.customer_id',
                'orderByValue' =>  'customers.name',
                'columnToJoin' => 'customers.id',
                'query' => $customerName];

        }

        if($request->query->get('orderBy') === 'project'  || isset($query->project)) {

            $filter = true;
            $projectName = isset($query->project) ? $query->project : '';
            $params[] = [
                'tableToJoin' => 'projects' ,
                'orderBy' => 'tasks.project_id',
                'orderByValue' =>  'projects.name',
                'columnToJoin' => 'projects.id',
                'query' => $projectName];
           // return (new Task())->sortBy($ascending, $request, $params);
        }
        if($request->query->get('orderBy') === 'status') {
            $filter = true;
            $params[] = [
                'tableToJoin' => 'statuses' ,
                'orderBy' => 'tasks.status_id',
                'orderByValue' =>  'statuses.name',
                'columnToJoin' => 'statuses.id',
                'query' => ''];
         //   return (new Task())->sortBy($ascending, $request, $params);
        }

        $orderByTaskValue = $request->query->get('orderBy');
        $hasRelation =  (new Task())->checkRelation($orderByTaskValue);
        $sortByTaskAttr = [$hasRelation, $orderByTaskValue];


        if ($filter) {

            $filter = true;

            return (new Task())->sortBy($ascending, $request, $params, $sortByTaskAttr);
        }



        if($request->query->get('orderBy') === 'category') {
            $params = [
                'tableToJoin' => 'categories' ,
                'orderBy' => 'tasks.category_id',
                'orderByValue' =>  'categories.name',
                'columnToJoin' => 'categories.id'];
            return (new Task())->sortBy($ascending, $request, $params);
        }

        $tasks = Task::with(['customer',  'project', 'priority', 'status','category'])->mine()->vueTable(Task::$columns);
        $filter = false;
        $tasksPercentage = (new Task())->getPercentage($tasks);
        return response()->json($tasksPercentage);
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
        $v['project_id'] = $request->request->get('project') ? $request->request->get('project')['id'] : null;
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
        $task->category = $task->getCategory($task->getAttribute('category_id'));
        $task->project = $task->getProject($task->getAttribute('project_id'));

        $tasksStatuses = (new Status)->getAllEntityStatus('App\Task');
        $tasksCategory = (new Category())->getAllEntityCategories('App\Task');
        $priorities = (new TaskPriority)->getAllTaskPriority()->toArray();
        $task->load($task->attributes->pluck('slug')->toArray());

        return [
            'task' => $task,
            'status' => $task->status,
            'priority' =>$task->priority,
            'customer' => $task->customer,
            'statuses' => $tasksStatuses,
            'priorities' => $priorities,
            'categories' => $tasksCategory,
            'category' => $task->category,
            'project' => $task->project
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
        $v['priority_id'] = $request->request->get('priority') ? $request->request->get('priority')['id'] : null;
        $v['customer_id'] = $request->request->get('customer') ? $request->request->get('customer')['id'] : null;
        $v['category_id'] = $request->request->get('category') ? $request->request->get('category')['id'] : null;
        $v['project_id'] = $request->request->get('project') ? $request->request->get('project')['id'] : null;
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
        $tasks = $this->filterBy($request, $customerId, 'customer_id');

        return response()->json($tasks);
    }

    public function getProjectTasks(Request $request ,$projectId)
    {
        $tasks = $this->filterBy($request, $projectId, 'project_id');

        return response()->json($tasks);
    }
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    private function filterBy($request, $entityId, $entityType)
    {
        $ascending = $request->request->get('ascending') == 1 ? 'DESC' : 'ASC';

        if ($request->query->get('query') == '{}') {
            $request->query->set('query', '');
        }
        $params = [];
        $query = '';
        $filter = false;
//        if($request->query->get('byColumn')) {
//            $ascending = $request->request->get('ascending') == 1 ? 'DESC' : 'ASC';
//            $requestOrderBy = $request->query->get('orderBy');
//            $orderBy = $requestOrderBy === 'priority' ? 'priority_id' : $requestOrderBy;
//            $request->query->set('orderBy', $orderBy);
//
//            $tasks = Task::where($entityType, $entityId)->orderBy($orderBy, $ascending)->with(['customer',  'project', 'priority', 'status','category'])->mine()->vueTable(Task::$columns);
//            return (new Task())->getPercentage($tasks);
//
//        }


        $ascending = $request->request->get('ascending') == 1 ? 'DESC' : 'ASC';

        if ($request->query->get('query') == '{}') {
            $request->query->set('query', '');
        }
        $params = [];
        $query = '';
        $filter = false;
        if(json_decode($request->query->get('query'))){
            $query = json_decode($request->query->get('query'));
        }

        if($request->query->get('orderBy') === 'priority') {

            $filter = true;
            $params[] = [
                'tableToJoin' => 'task_priorities' ,
                'orderBy' => 'tasks.priority_id',
                'orderByValue' => 'task_priorities.id',
                'columnToJoin' => 'task_priorities.id',
                'query' => ''];

        }

        if($request->query->get('orderBy') === 'customer' || isset($query->customer)) {
            $filter = true;
            $customerName = isset($query->customer) ? $query->customer : '';
            $params[] = [
                'tableToJoin' => 'customers' ,
                'orderBy' => 'tasks.customer_id',
                'orderByValue' =>  'customers.name',
                'columnToJoin' => 'customers.id',
                'query' => $customerName];

        }

        if($request->query->get('orderBy') === 'project'  || isset($query->project)) {

            $filter = true;
            $projectName = isset($query->project) ? $query->project : '';
            $params[] = [
                'tableToJoin' => 'projects' ,
                'orderBy' => 'tasks.project_id',
                'orderByValue' =>  'projects.name',
                'columnToJoin' => 'projects.id',
                'query' => $projectName];
            // return (new Task())->sortBy($ascending, $request, $params);
        }
        if($request->query->get('orderBy') === 'status') {
            $filter = true;
            $params[] = [
                'tableToJoin' => 'statuses' ,
                'orderBy' => 'tasks.status_id',
                'orderByValue' =>  'statuses.name',
                'columnToJoin' => 'statuses.id',
                'query' => ''];
            //   return (new Task())->sortBy($ascending, $request, $params);
        }

        $orderByTaskValue = $request->query->get('orderBy');
        $hasRelation =  (new Task())->checkRelation($orderByTaskValue);
        $sortByTaskAttr = [$hasRelation, $orderByTaskValue];


        if ($filter) {

            $filter = true;
            $filterByEntity = ['entityType' =>$entityType, 'entityId' => $entityId];
            return (new Task())->sortBy($ascending, $request, $params, $sortByTaskAttr, $filterByEntity)->original;
        }



        if($request->query->get('orderBy') === 'category') {
            $params = [
                'tableToJoin' => 'categories' ,
                'orderBy' => 'tasks.category_id',
                'orderByValue' =>  'categories.name',
                'columnToJoin' => 'categories.id'];
            return (new Task())->sortBy($ascending, $request, $params);
        }

        $filter = false;
       $tasks = Task::where($entityType, $entityId)->with(['customer',  'project', 'priority', 'status','category'])->mine()->vueTable(Task::$columns);

        $tasksPercentage = (new Task())->getPercentage($tasks);

        return response()->json($tasksPercentage)->original;


    }
}
