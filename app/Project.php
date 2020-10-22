<?php

namespace App;

use App\Http\ModelForm;
use App\Traits\AccountingJournal;
use App\Traits\AttributableModel;
use App\Traits\DynamicHiddenVisible;
use App\Traits\LogActivity;
use App\Traits\Restrictable;
use App\Traits\VueTable;
use Illuminate\Support\Facades\DB;


class Project extends ModelForm
{
    use AccountingJournal, AttributableModel, LogActivity, Restrictable, VueTable;

    public static $columns = ['id', 'name', 'customer_id', 'type_id', 'price', 'start_date', 'end_date', 'expenses'];

    protected $fillable = ['code', 'name', 'customer_id', 'type_id', 'price', 'start_date', 'end_date', 'expenses'];
    protected $hidden = ['created_at', 'updated_at'];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function type()
    {
        return $this->belongsTo(ProjectTypes::class);
    }

    public function getType($typeId)
    {

        return (new ProjectTypes())->getPriorityById($typeId);
    }

    public function getCustomer($customerId)
    {

        return (new Customer())->getCustomerById($customerId);
    }


    public function scopeSearch($query, $search)
    {
        if (!empty($search)) {
            $query->where(function ($query) use ($search) {
                $query->where('name', 'LIKE', "%$search%");
            });
        }
        return $query;
    }

    public function getProjectById($projectId)
    {
        return DB::table('projects')->select('id', 'name')
            ->where('id', $projectId)
            ->get();
    }

    public function getPercentageDone($projects)
    {
        $projectsIds = array_column($projects["data"], 'id');

        $projectsTasks = DB::table('tasks')->select('id', 'name', 'actual_time', 'project_id')
            ->whereIn('project_id', $projectsIds)
            ->get();

        foreach ($projectsTasks as $projectTask) {
            foreach ($projects['data'] as $key => $project) {

                if ($project['id'] === $projectTask->project_id) {
                    $project['actual_time'][] = $projectTask->actual_time;

                    $projects['data'][$key] = $project;
                }
            }
        }
        return $projects;
    }
}
