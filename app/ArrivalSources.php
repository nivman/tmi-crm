<?php

namespace App;

use App\Traits\LogActivity;
use App\Traits\Restrictable;
use App\Traits\VueTable;
use Illuminate\Database\Eloquent\Model;

class ArrivalSources extends Model
{
    use LogActivity, Restrictable, VueTable;
    public static $columns = ['id', 'name'];
    protected $fillable = ['name',  'color'];


    public function getArrivalSourcesById($id)
    {
        dd($id);
    }
}
