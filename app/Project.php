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

    public static $columns = ['id', 'name', 'customer_id', 'type_id', 'price', 'start_date', 'end_date', 'expenses', 'active'];

    protected $fillable = ['code', 'name', 'customer_id', 'type_id', 'price', 'start_date', 'end_date', 'expenses', 'active'];
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

    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }

    public function upSale()
    {
        return $this->hasMany(UpSale::class);
//        $data = UpSale::with(['project' => function($query) {
//            $query->sum('amount');
//            ;
//        }])->get(['title','project_id', DB::raw('amount')])->toArray();
//
//
//
//
//dd($data);
//
//
//
//        return $this->hasMany(UpSale::class)->selectRaw('SUM(amount) as amount')
//            ->groupBy('project_id');;
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

    public function getProjectByIds($projectId)
    {
        return DB::table('projects')->select('id', 'name')
            ->whereIn('id', $projectId)
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

    public function projectPercentageDone($project)
    {
        $projectsTasks = DB::table('tasks')->select('id', 'name', 'actual_time', 'project_id')
            ->where('project_id', $project->id)
            ->get();
        $collectTaskTime = [];
        foreach ($projectsTasks as $projectsTask) {
            $collectTaskTime[] = $projectsTask->actual_time;
        }
        //TODO hour wage should be dynamic
        $HourlyWage = 230;
        $convertToHours = (array_sum($collectTaskTime) / 60);
        $totalTimeAsAmount = $convertToHours *  $HourlyWage;

        if (!$project->price) {

            return '';
        }
        $percentage = $totalTimeAsAmount / $project->price;
        return number_format((float)$percentage * 100, 2, '.', '');

    }

    public function getProjectsCustomersByIds($customerIds)
    {
        $ids = explode(',', $customerIds);

        return DB::table('projects')->select('projects.*')

            ->leftJoin('customers as cu', 'cu.id', '=', 'projects.customer_id')
            ->leftJoin('contacts as co', 'co.customer_id', '=', 'cu.id')
            ->whereIn('projects.customer_id', $ids)
            ->distinct()
            ->get();
    }

    public function sumTasksTimeByCustomersId($customerIds)
    {
        $ids = explode(',', $customerIds);

        $customersSumTime = DB::table('projects as p')->select('cu.id as cu_id', DB::raw('SUM(price) AS price'))
            ->leftJoin('customers as cu', 'cu.id', '=', 'p.customer_id')
            ->groupBy('p.customer_id')
            ->whereIn('p.customer_id', $ids)
            ->get()->toArray();

        $customerTasksTime = (new Task())->sumTasksTimeByCustomersId( array_column($customersSumTime,'cu_id'));

        foreach ($customersSumTime as   $customerSumTime) {
            foreach ($customerTasksTime as   $customerTaskTime) {
                if($customerSumTime->cu_id == $customerTaskTime->customer_id) {
                    $key = array_search($customerTaskTime, $customerTasksTime);
                    $customerTasksTime[$key]->price = $customerSumTime->price;
                    $customerPrice = $customerSumTime->price ? $customerSumTime->price : 0;
                    $customerTasksTime[$key]->percentage  = $customerPrice ?$customerTaskTime->total_money / $customerPrice * 100 : null;
               }

            }
        }

        $customerTasksTime = json_decode(json_encode($customerTasksTime), true);
        $customerTasksTime =array_column($customerTasksTime, null, 'customer_id');
        return $customerTasksTime;

    }

    public function setProjectTotalPrice($projects)
    {
        $finalPrice = [];
        foreach ($projects['data'] as $project) {
            $upSales = array_sum(array_column($project['up_sale'], 'amount'));
            $project['originalPrice'] = $project['price'];
            $project['price']  = $project['price'] + $upSales;

            $finalPrice[] = $project;

        }

        $projects['data'] = $finalPrice;

        return $projects;
    }
}
