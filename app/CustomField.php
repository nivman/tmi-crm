<?php

namespace App;

use App\Traits\VueTable;
use App\Traits\LogActivity;
use Rinvex\Attributes\Models\Attribute;

class CustomField extends Attribute
{
    use LogActivity, VueTable;

    public static $columns = ['id', 'name', 'slug', 'type', 'description', 'sort_order', 'is_required'];

    protected $hidden = ['created_at', 'updated_at'];
    protected $with   = ['entities'];
}
