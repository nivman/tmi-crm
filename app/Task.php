<?php

namespace App;

use App\Http\ModelForm;
use App\Notifications\TaskNotification;
use App\Traits\AccountingJournal;
use App\Traits\AttributableModel;
use App\Traits\LogActivity;
use App\Traits\Restrictable;
use App\Traits\VueTable;
use Carbon\Carbon;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Support\Facades\DB;
use Thomasjohnkane\Snooze\ScheduledNotification;

class Task extends ModelForm
{
    use AttributableModel, LogActivity, Restrictable, VueTable;

    public static $columns = ['id', 'name', 'details', 'start_date', 'end_date', 'estimated_time', 'actual_time', 'project_id', 'date_to_complete', 'customer.name', 'project.name', 'repeat', 'contact.first_name', 'contact.last_name'];
    protected $fillable = ['id', 'name', 'details', 'start_date', 'end_date', 'customer_id', 'customer_name', 'priority_id', 'status_id', 'estimated_time', 'actual_time', 'date_to_complete', 'notification_time', 'category_id', 'project_id', 'notification_enable', 'repeat', 'contact_id'];
    protected $hidden = ['created_at', 'updated_at'];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function contact()
    {
        return $this->belongsTo(Contact::class);
    }

    public function priority()
    {
        return $this->belongsTo(TaskPriority::class);
    }

    public function status()
    {
        return $this->belongsTo(Status::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function getPriority($priorityId)
    {
        return (new TaskPriority())->getPriorityById($priorityId);
    }

    public function getCustomer($customerId)
    {
        return (new Customer())->getCustomerById($customerId);
    }

    public function getContact($contactId)
    {
        return Contact::find($contactId);
    }

    public function getCategory($categoryId)
    {
        return (new Category())->getCategoryById($categoryId);
    }

    public function getProject($projectId)
    {
        return (new Project())->getProjectById($projectId);
    }

    public function repeatRules()
    {

        return $this->hasMany(TasksEventsRepeatRule::class);
    }

    public function taskRepeat()
    {
        return $this->hasMany(TasksEventsRepeat::class);
    }

    public function getPercentage($tasks)
    {

        $projectsIds = array_column($tasks['data'], 'project_id');

        $projects = DB::table('projects')->select('id', 'name', 'price')
            ->whereIn('id', $projectsIds)
            ->get();

        foreach ($tasks['data'] as $key => $task) {
            foreach ($projects as $project) {
                if ($project->id == $task['project_id']) {
                    $task['projectPrice'] = $project->price;
                    $tasks['data'][$key] = $task;
                }
            }
        }

        return $tasks;
    }

    public function sortBy($ascending, $request, $params, $sortByTaskAttr = [], $filterByEntity =[])
    {

        $query = Task::query();
        $result = null;

        $request->query->set('orderBy', $params[0]['orderByValue']);

        foreach ($params as $key => $param) {

            $result = $query->leftJoin($params[$key]['tableToJoin'], $params[$key]['orderBy'], '=', $params[$key]['columnToJoin'])
                ->select('tasks.*')
                ->where(function ($q) use ($params, $key, $filterByEntity) {
                    if (count($filterByEntity) > 0) {

                        $q->where('tasks.'.$filterByEntity['entityType'], '=', $filterByEntity['entityId']);

                        if ($params[0]['tableToJoin'] === 'categories' || $params[0]['tableToJoin'] === 'statuses' || $params[0]['tableToJoin'] === 'task_priorities') {
                            $q->OrWhere($params[$key]['orderByValue'], 'LIKE', "%{$params[$key]['query']}%")->where($params[$key]['orderBy'], '=', null);

                        }else {
                            $q->where($params[$key]['orderByValue'], 'LIKE', "%{$params[$key]['query']}%") ;
                        }
                    }else{

                        $q->where($params[$key]['orderByValue'], 'LIKE', "%{$params[$key]['query']}%");

                        if ($params[$key]['query'] == '') {

                            $q->orWhere($params[$key]['orderByValue'], '=', null);
                        }
                    }
                })
                ->with(['customer', 'contact', 'project', 'priority', 'status', 'category', 'taskRepeat','repeatRules'])
                ->mine()
                ->orderBy($params[$key]['orderByValue'], $ascending)
                ->vueTable(Task::$columns, false, 'tasks');

        }

        $tasks['data'] = $query->get()->toArray();
        $tasks['count'] = $result['count'];

        $tasksPercentage = $this->getPercentage($tasks);

        return response()->json($tasksPercentage);
    }

    public function checkRelation($key)
    {
        return in_array($key, Task::$columns);

    }

    public function getTasksByCustomerId($customerId)
    {
        $tasks =  DB::table('tasks')->select('*')
            ->where('customer_id', $customerId)
            ->get()->toArray();

        return Task::hydrate($tasks);
    }

    public function sumTasksTimeByProjectsId($projectsId)
    {

        return  DB::table('tasks', 't')
            ->whereIn('t.project_id', $projectsId)
            ->leftJoin('categories as c', 't.category_id', '=', 'c.id')
            ->leftJoin('projects as p', 't.project_id', '=', 'p.id')
            ->groupBy('t.category_id')
            ->groupBy('t.project_id')
            ->get([ 't.project_id', 'p.name as project_name', 'p.price as project_price', 'c.name', 't.category_id',  DB::raw('CONCAT(Floor(SUM(actual_time) /60), ".",LPAD(ROUND((SUM(actual_time) /60 - Floor(SUM(actual_time) /60)) * 60 % 60),2,"0")) AS actual_time')])->toArray();
    }

    public function sumTasksTimeByCategoriesId($startDate, $endDate)
    {

        return  DB::table('tasks', 't')
            ->whereBetween('start_date',[$startDate,$endDate])
            ->leftJoin('categories as c', 't.category_id', '=', 'c.id')
            ->groupBy('t.category_id')
            ->get(['t.category_id', 'c.name', 't.category_id',  DB::raw('CONCAT(Floor(SUM(actual_time) /60), ".",LPAD(ROUND((SUM(actual_time) /60 - Floor(SUM(actual_time) /60)) * 60 % 60),2,"0")) AS actual_time')])->toArray();
    }

    public function sumTasksTimeByCustomersId($customersId)
    {

        return  DB::table('tasks', 't')
            ->whereIn('t.customer_id', $customersId)
            ->leftJoin('customers as cu', 't.customer_id', '=', 'cu.id')
            ->groupBy('t.customer_id')
            ->get([
                't.customer_id',
                'cu.name as customer_name',
                DB::raw('SUM(actual_time) AS actual_time_min'),
                DB::raw(' (SUM(actual_time) / 60) as total_hours'),
                DB::raw(' ((SUM(actual_time) / 60) * 150) as total_money'),
                ])->toArray();
    }


    public function getTasksByProjectsStartDate()
    {

        $projectsId =  DB::table('projects', 'p')->select('id')
            ->orderBy('p.start_date', 'desc')
            ->limit(3)->get()->toArray();

        return $this->sumTasksTimeByProjectsId(array_column($projectsId, 'id'));

    }

    public function sumProjectActualTime($projectsId)
    {
        return DB::table('tasks')
            ->whereIn('project_id', $projectsId)
            ->groupBy('project_id')
            ->get(['project_id', DB::raw('SUM(actual_time /60) AS original_actual_time') , DB::raw('CONCAT(Floor(SUM(actual_time) /60), ".",LPAD(ROUND((SUM(actual_time) /60 - Floor(SUM(actual_time) /60)) * 60 % 60),2,"0")) AS actual_time')])->toArray();
    }

    public function setNotification($taskCreated)
    {
        if (!$taskCreated->notification_time) {
            return false;
        }
        $target = (new AnonymousNotifiable)
            ->route('mail', env('MAIL_USERNAME'));

        ScheduledNotification::create(
            $target,
            new TaskNotification($taskCreated),
            Carbon::parse($taskCreated->notification_time)
        );
        return true;
    }

    public function unHandledTaskNotification () {

        $date = Carbon::now('Asia/Jerusalem')->subMinute(2);

        $where = [['notification_enable' ,'=', 1], ['notification_time' ,'<=', $date]];
        return DB::table('tasks')->select('*')
            ->where($where)
            ->get()->toArray();


    }
}
