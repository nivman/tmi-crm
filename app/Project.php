<?php

namespace App;

use App\Http\ModelForm;
use App\Traits\AttributableModel;
use App\Traits\DynamicHiddenVisible;
use App\Traits\LogActivity;
use App\Traits\VueTable;


class Project extends ModelForm
{
    use AttributableModel, DynamicHiddenVisible, LogActivity, VueTable;
    public static $columns = ['id', 'name','customer_id','type_id', 'price','start_date', 'end_date'];

    protected $fillable = ['code', 'name','customer_id','type_id', 'price','start_date', 'end_date'];
}
