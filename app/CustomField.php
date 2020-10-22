<?php

namespace App;

use App\Traits\VueTable;
use App\Traits\LogActivity;
use Illuminate\Support\Facades\DB;
use Rinvex\Attributes\Models\Attribute;

class CustomField extends Attribute
{
    use LogActivity, VueTable;

    public static $columns = ['id', 'name', 'slug', 'type', 'description', 'sort_order', 'is_required'];

    protected $hidden = ['created_at', 'updated_at'];
    protected $with   = ['entities'];

    public static function whereSlug($value)
    {
        return DB::table('attributes')->select('name')
            ->where('slug',$value)
            ->get();
    }
}
