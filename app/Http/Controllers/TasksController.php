<?php

namespace App\Http\Controllers;

use App\Category;
use App\Customer;
use App\Helpers\Filters;
use App\Http\Requests\TaskRequest;
use App\Notifications\TaskNotification;
use App\Project;
use App\Status;
use App\Task;
use App\TaskPriority;

use App\TasksEventsRepeat;
use App\TasksEventsRepeatRule;
;

use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

use Recurr\Recurrence;



/**
 * Class TasksController
 * @package App\Http\Controllers
 */
class TasksController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        $ascending = $request->request->get('ascending') == 1 ? 'DESC' : 'ASC';

        $params = Filters::filters($request, 'tasks');

        $orderByTaskValue = $request->query->get('orderBy');

        $hasRelation = (new Task())->checkRelation($orderByTaskValue);

        $sortByTaskAttr = [$hasRelation, $orderByTaskValue];

        if ($params['filter']) {

            return (new Task())->sortBy($ascending, $request, $params['params'], $sortByTaskAttr);
        }

        $tasks = Task::with(['customer', 'contact', 'project', 'priority', 'status', 'category', 'taskRepeat','repeatRules'])->mine()->vueTable(Task::$columns);

        $tasksPercentage = (new Task())->getPercentage($tasks);
        return response()->json($tasksPercentage);
    }

    public function create()
    {
        $taskStatuses = (new Status)->getAllEntityStatus('App\Task');
        $priorities = (new TaskPriority)->getAllTaskPriority()->toArray();
        $taskCategories = (new Category())->getAllEntityCategories('App\Task');

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
        $TASK_REPEAT = 1;
        $v = $request->validated();
        $v['customer_id'] = $request->request->get('customer') ? $request->request->get('customer')['id'] : null;
        $v['contact_id'] = $request->request->get('contact') ? $request->request->get('contact')['id'] : null;
        $v['priority_id'] = $request->request->get('priority') ? $request->request->get('priority')['id'] : null;
        $v['category_id'] = $request->request->get('category') ? $request->request->get('category')['id'] : null;
        $v['status_id'] = $request->request->get('status') ? $request->request->get('status')['id'] : null;
        $v['project_id'] = $request->request->get('project') ? $request->request->get('project')['id'] : null;


        $task = new Task();
        $taskCreated = Task::create($v);
        if ($request->request->get('repeat') === $TASK_REPEAT) {
          (new TasksEventsRepeat)->setRecurrence($taskCreated,  $request->request, 'App\Task');
          (new TasksEventsRepeatRule())->setRule($taskCreated,  $request->request);
         }
        (new Task)->setNotification($taskCreated);

        return $task;
    }


    public function show(Task $task, Request $request)
    {
        $repeatTask = null;
        $repeatTaskRuleText = null;
        if($request->get('repeatTaskId') !== "undefined" ){
            $repeatTask = TasksEventsRepeat::where('id',$request->get('repeatTaskId'))->with(['status'])->get()->toArray() ;
            $repeatTaskRuleText = TasksEventsRepeatRule::where('task_id', $task->id)->select(['text_rule'])->get()->toArray();

        }
        $task->attributes = $task->attributes();
        $task->status = $task->getStatus($task->getAttribute('status_id'));
        $task->priority = $task->getPriority($task->getAttribute('priority_id'));
        $task->customer = $task->getCustomer($task->getAttribute('customer_id'));
        $task->contact = $task->getContact($task->getAttribute('contact_id'));
        $task->category = $task->getCategory($task->getAttribute('category_id'));
        $task->project = $task->getProject($task->getAttribute('project_id'));

        $tasksStatuses = (new Status)->getAllEntityStatus('App\Task');
        $tasksCategory = (new Category())->getAllEntityCategories('App\Task');
        $priorities = (new TaskPriority)->getAllTaskPriority()->toArray();
        $task->load($task->attributes->pluck('slug')->toArray());

        return [
            'task' => $task,
            'status' => $task->status,
            'priority' => $task->priority,
            'customer' => $task->customer,
            'contact' => $task->contact,
            'statuses' => $tasksStatuses,
            'priorities' => $priorities,
            'categories' => $tasksCategory,
            'category' => $task->category,
            'project' => $task->project,
            'repeatTask' => $repeatTask,
            'repeatTaskRuleText' => $repeatTaskRuleText
        ];
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Task $task
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
        $v['contact_id'] = $request->request->get('contact') ? $request->request->get('contact')['id'] : null;
        $v['category_id'] = $request->request->get('category') ? $request->request->get('category')['id'] : null;
        $v['project_id'] = $request->request->get('project') ? $request->request->get('project')['id'] : null;
        $task->update($v);

        (new Task)->setNotification($task);
        return $task;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Task $task
     * @return \Illuminate\Http\Response
     */
    public function destroy(Task $task)
    {
        if($task->repeat) {
            TasksEventsRepeatRule::where(['task_id' => $task->id])->delete();
            TasksEventsRepeat::where(['task_id' => $task->id])->delete();

        }
        $task->delete();

        return response(['success' => true], 204);
    }

    public function destroyRepeatTask($repeatTaskId)
    {
        TasksEventsRepeat::find($repeatTaskId)->update(['show' => 0]);
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

    public function getProject($id)
    {
        return Project::find($id);
    }

    public function getCustomerTasks(Request $request, $customerId)
    {
        $tasks = $this->filterBy($request, $customerId, 'customer_id');

        return response()->json($tasks);
    }

    public function getProjectTasks(Request $request, $projectId)
    {
        $tasks = $this->filterBy($request, $projectId, 'project_id');

        return response()->json($tasks);
    }

    public function updateCalendarDates(Request $request)
    {
        $eventId = $request->request->get('event')['event']['id'];
        $request->request->set('start_date', $request->request->get('start'));
        $request->request->set('end_date', $request->request->get('end'));
        $v = ['start_date' => $request->request->get('start'), 'end_date' => $request->request->get('end')];

        $event = Task::find($eventId);
        $event->update($v);
        return $event;
    }

    public function updateCalendarRepeatDates(Request $request)
    {
        $MONTHLY = 1;
        $WEEKLY = 2;


        $modifyAllEvents = $request->request->get('toAllEvents');
        $createNewEvent = $request->request->get('createNewTaskEvent');

        if ($createNewEvent) {
            $taskId = $request->request->get('event')['event']['extendedProps']['task_id'];
            $task = Task::find($taskId);
            $startDate   = new \DateTime($request->request->get('start'));

            $endDate   = new \DateTime($request->request->get('end'));
            $repeatTaskId = $request->request->get('event')['event']['id'];
            $this->createNewTaskFromRepeatedTask($task, $startDate, $endDate, $repeatTaskId);
            return;
        }
        if ($modifyAllEvents) {

            $taskId = $request->request->get('event')['event']['extendedProps']['task_id'];

            $allRepeatedTasks = (new TasksEventsRepeatRule())->getAllRepeatedTasks($taskId);

            $eventRules = json_decode(TasksEventsRepeatRule::where('task_id',$taskId)->get()->toArray()[0]['rules'],true);
            $freq = (new TasksEventsRepeatRule)->getFraq($eventRules['freq']);
            $days = (new TasksEventsRepeatRule)->getDays(array_column($eventRules['byweekday'], 'weekday'));
            $startDate   = new \DateTime($request->request->get('start'));

            $endDate   = new \DateTime($request->request->get('end'));

            $rule = (new \Recurr\Rule)
                ->setStartDate($startDate)
                ->setEndDate($endDate)
                ->setCount($eventRules['count'])
                ->setFreq($freq);
            if ($eventRules['freq'] === $WEEKLY){
                $rule->setByDay($days);

            }elseif ($eventRules['freq'] === $MONTHLY){
                $rule->setByMonthDay(array_column($eventRules['byweekday'], 'weekday'));
            }

            if(($eventRules['until']) ){

                $rule->setUntil(Carbon::parse($eventRules['until']));
            }
            $rule->setInterval($eventRules['interval']);
            $transformer = new \Recurr\Transformer\ArrayTransformer();

            $newDates =  $transformer->transform($rule)->toArray();

            /** @var Recurrence $newDate */
            foreach ($newDates as $key => $newDate) {
                $v = ['start_date' => $newDate->getStart(), 'end_date' => $newDate->getEnd()];
                TasksEventsRepeat::where('id',$allRepeatedTasks[$key]['id'])->update($v);
            }

        }else{
            $repeatedTaskId = $request->request->get('event')['event']['id'];

            $start = $request->request->get('event')['event']['start'];
            $end = $request->request->get('event')['event']['end'];
            $start = Carbon::parse($start)->format('Y-m-d H:i');
            $end = Carbon::parse($end)->format('Y-m-d H:i');

            $v = ['start_date' => $start, 'end_date' => $end];
            TasksEventsRepeat::find($repeatedTaskId)->update($v);

        }

    }

    public function unseenTask()
    {
        return (new Task())->unHandledTaskNotification();
    }

    public function cancelNotification(Task $task)
    {
        $task->update(['notification_enable' => 0]);
    }

    public function updateAllRepeatedTasks(Request $request)
    {
        $taskId = $request->request->get('id');
        $status_id = $request->request->get('status')['id'];
        $name = $request->request->get('name');
        $details = $request->request->get('details');

        $vtr = ['status_id' => $status_id, 'name' => $name, 'details' => $details ];
        TasksEventsRepeat::where(['task_id' => $taskId])->update($vtr);

        $this->updateMainTaskFromRepeat($request);

        return response(['success' => true], 204);

    }

    public function updateSingleRepeatedTasks(Request $request)
    {
        $repeatTaskId = $request->request->get('repeatTaskId');

        $status_id = $request->request->get('status')['id'];
        $name = $request->request->get('name');
        $details = $request->request->get('details');
        $v = ['status_id' => $status_id, 'name' => $name, 'details' => $details ];
        TasksEventsRepeat::find($repeatTaskId)->update($v);
        $this->updateMainTaskFromRepeat($request);
        return response(['success' => true], 204);
    }

    public function duplicate($id)
    {
        $task = Task::find($id);
        $record = $task->replicate();
        $record->save();
        return response(['success' => true], 204);
    }
    private function updateMainTaskFromRepeat($request)
    {

        $taskId = $request->request->get('id');
        $categoryId = $request->request->get('category') ? $request->request->get('category')['id'] : null;
        $priorityId = $request->request->get('priority') ? $request->request->get('priority')['id'] : null;
        $projectId = $request->request->get('project') ? $request->request->get('project')['id'] : null ;
        $name = $request->request->get('name');
        $details = $request->request->get('details');
        $vt = ['category_id' => $categoryId, 'priority_id' => $priorityId, 'project_id' => $projectId, 'name' => $name, 'details' => $details];
        Task::where(['id' => $taskId])->update($vt);
    }

    private function filterBy($request, $entityId, $entityType)
    {

        $ascending = $request->request->get('ascending') == 1 ? 'DESC' : 'ASC';

        $params = Filters::filters($request, 'tasks');

        $orderByTaskValue = $request->query->get('orderBy');

        $hasRelation = (new Task())->checkRelation($orderByTaskValue);
        $sortByTaskAttr = [$hasRelation, $orderByTaskValue];

        if ($params['filter']) {

            $filterByEntity = ['entityType' => $entityType, 'entityId' => $entityId];
            return (new Task())->sortBy($ascending, $request, $params['params'], $sortByTaskAttr, $filterByEntity)->original;
        }

        $tasks = Task::where($entityType, $entityId)->with(['customer', 'project', 'priority', 'status', 'category'])->mine()->vueTable(Task::$columns);

        $tasksPercentage = (new Task())->getPercentage($tasks);

        return response()->json($tasksPercentage)->original;


    }

    private function createNewTaskFromRepeatedTask($task, \DateTime $startDate, \DateTime $endDate, $repeatTaskId)
    {
        $start = Carbon::parse($startDate)->format('Y-m-d H:i');
        $end = Carbon::parse($endDate)->format('Y-m-d H:i');

        $taskToArray = $task->toArray();

        $taskToArray['start_date'] = $start;
        $taskToArray['end_date'] = $end;
        $taskToArray['id'] = null;
        $taskToArray['repeat'] = 0;
        Task::create($taskToArray);

       TasksEventsRepeat::find($repeatTaskId)->update(['show' => 0]);

    }

}
