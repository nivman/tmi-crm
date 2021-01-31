<?php

namespace App;

use App\Http\ModelForm;
use Ulid\Ulid;
use App\Traits\VueTable;
use App\Traits\LogActivity;
use App\Traits\Restrictable;
use App\Traits\AttributableModel;


class Expense extends ModelForm
{
    use AttributableModel, LogActivity, Restrictable, VueTable;

    public static $columns = ['id', 'created_at', 'title', 'reference', 'amount', 'vendor.name','categories.name', 'account.name', 'project.name'];

    protected $fillable = ['id', 'title', 'amount', 'reference', 'details', 'account_id', 'user_id', 'project_id', 'vendor_id', 'category_id'];
    protected $hidden   = ['updated_at'];
    protected $with     = ['account', 'categories', 'project', 'vendor'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (!$model->reference) {
                $model->reference = Ulid::generate(true);
            }
        });

        static::deleting(function ($expense) {
            $expense->categories()->detach();
        });
    }

    public function vendor()
    {

        return $this->belongsTo(Vendor::class);
    }

    public function account()
    {

        return $this->belongsTo(Account::class);
    }

    public function project()
    {

        return $this->belongsTo(Project::class);
    }

    public function categories()
    {
        return $this->morphToMany(Category::class, 'categorizable');
    }

    public function taxes()
    {
        return $this->morphToMany(Tax::class, 'taxable');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function checkRelation($key)
    {
        return in_array($key, Expense::$columns);

    }

    public function sortBy($ascending, $request, $params, $sortByTaskAttr = [], $filterByEntity =[])
    {

        $query = Expense::query();
        $result = null;

        $request->query->set('orderBy', $params[0]['orderByValue']);
        $projectId = $request->query->get('projectId');

        foreach ($params as $key => $param) {

            $result = $query->leftJoin($params[$key]['tableToJoin'], $params[$key]['orderBy'], '=', $params[$key]['columnToJoin'])
                ->select('expenses.*')
                ->where(function ($q) use ($params, $key, $filterByEntity, $projectId) {

                    if (count($filterByEntity) > 0) {

                        $q->where('expenses.'.$filterByEntity['entityType'], '=', $filterByEntity['entityId']);

                        if ($params[0]['tableToJoin'] === 'events_types' ) {
                            $q->OrWhere($params[$key]['orderByValue'], 'LIKE', "%{$params[$key]['query']}%")->where($params[$key]['orderBy'], '=', null);

                        }else {

                            $q->where($params[$key]['orderByValue'], 'LIKE', "%{$params[$key]['query']}%") ;
                        }
                    }else{
                        if ($projectId) {
                            $q->where($params[$key]['orderBy'], '=', $projectId);
                        }else {
                            $q->where($params[$key]['orderByValue'], 'LIKE', "%{$params[$key]['query']}%");
                        }
                    }
                })
                ->with(['project'])
                ->mine()
                ->orderBy($params[$key]['orderByValue'], $ascending)
                ->vueTable(Expense::$columns, false, 'expenses');

        }

        $events['data'] = $query->get()->toArray();
        $events['count'] = $result['count'];
        return response()->json($events);
    }
}
