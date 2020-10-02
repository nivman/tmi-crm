<?php

namespace App;

use Ulid\Ulid;
use App\Traits\VueTable;
use App\Traits\LogActivity;
use App\Traits\Restrictable;
use App\Traits\AttributableModel;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use AttributableModel, LogActivity, Restrictable, VueTable;

    public static $columns = ['id', 'created_at', 'title', 'reference', 'amount', 'categories.name', 'account.name'];

    protected $fillable = ['id', 'title', 'amount', 'reference', 'details', 'account_id', 'user_id'];
    protected $hidden   = ['updated_at'];
    protected $with     = ['account', 'categories'];

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

    public function account()
    {
        return $this->belongsTo(Account::class);
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
