<?php

namespace App;

use App\Traits\LogActivity;
use App\Traits\Restrictable;
use App\Traits\VueTable;
use Illuminate\Database\Eloquent\Model;

class NotesCategories extends Model
{
    use  LogActivity, Restrictable, VueTable;

    public static $columns = [ 'name', 'color' ];

    protected $fillable = ['name', 'color', 'created_at'];
}
