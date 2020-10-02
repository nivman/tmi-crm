<?php

namespace App;

use App\Traits\VueTable;
use App\Traits\LogActivity;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use LogActivity, VueTable;

    public static $columns = ['id', 'name'];

    protected $fillable = ['name'];
    protected $hidden   = ['created_at', 'updated_at'];

    public function expenses()
    {
        return $this->morphedByMany(Expense::class, 'categorizable');
    }

    public function incomes()
    {
        return $this->morphedByMany(Income::class, 'categorizable');
    }

    public function products()
    {
        return $this->morphedByMany(Product::class, 'categorizable');
    }
}
