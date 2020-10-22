<?php

namespace App;

use App\Http\ModelForm;
use App\Traits\AccountingJournal;
use App\Traits\AttributableModel;
use App\Traits\LogActivity;
use App\Traits\Restrictable;
use App\Traits\VueTable;
use Illuminate\Support\Facades\DB;

class Task extends ModelForm
{
    use AttributableModel, LogActivity, Restrictable, VueTable;

    public static $columns = ['id', 'name', 'details', 'start_date', 'end_date', 'estimated_time', 'actual_time', 'date_to_complete'];
    protected $fillable = ['id', 'name', 'details', 'start_date', 'end_date', 'customer_id', 'priority_id', 'status_id', 'estimated_time', 'actual_time', 'date_to_complete', 'notification_time', 'category_id', 'project_id'];
    protected $hidden = ['created_at', 'updated_at'];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
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

    public function getCategory($categoryId)
    {
        return (new Category())->getCategoryById($categoryId);
    }

    public function getProject($projectId)
    {
        return (new Project())->getProjectById($projectId);
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
}
