<?php

namespace App;

use App\Http\ModelForm;
use App\Traits\VueTable;
use App\Traits\LogActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Category extends ModelForm
{
    use LogActivity, VueTable;

    public static $columns = ['id', 'name','entity_name'];

    protected $fillable = ['name','entity_name'];
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

    public function getAllEntityCategories($entityName)
    {
        return DB::table('categories')->select('id', 'name')
            ->where('entity_name', $entityName)
            ->get();
    }

    public function getCategoryById($categoryId)
    {
        return DB::table('categories')->select('id', 'name')
            ->where('id', $categoryId)
            ->get();
    }
}
