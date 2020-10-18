<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class TaskPriority extends Model
{
    public static $columns = ['id', 'name',  'color'];
    protected $fillable = ['id', 'name', 'color'];

    public function getAllTaskPriority() {
        return DB::table('task_priorities')->select('id', 'name', 'color')->get();
    }

    public function getPriorityById($id) {
        return DB::table('task_priorities')->select('id', 'name', 'color')
            ->where('id', $id)
            ->get();
    }
}
