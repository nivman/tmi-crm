<?php

namespace App;

use App\Traits\VueTable;
use App\Traits\LogActivity;
use Illuminate\Support\Facades\DB;
use Rinvex\Attributes\Models\Attribute;

class CustomField extends Attribute
{
    use LogActivity, VueTable;

    public static $columns = ['id', 'name', 'slug', 'type', 'description', 'sort_order', 'is_required', 'hide_in_list'];
    protected $fillable = [
        'name',
        'slug',
        'description',
        'sort_order',
        'group',
        'type',
        'is_required',
        'is_collection',
        'default',
        'entities','hide_in_list'
    ];
    protected $hidden = ['created_at', 'updated_at'];
    protected $with   = ['entities'];

    public static function whereSlug($value)
    {
        $where = ['slug' => $value, 'hide_in_list' => 0];
        return DB::table('attributes')->select('name')
            ->where($where)
            ->get();
    }
}
