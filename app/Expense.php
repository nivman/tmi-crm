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

    public static $columns = ['id', 'created_at', 'title', 'reference', 'amount', 'categories.name', 'account.name', 'projects.name', 'vendor.name'];

    protected $fillable = ['id', 'title', 'amount', 'reference', 'details', 'account_id', 'user_id', 'project_id', 'vendor_id'];
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
}
