<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ProjectTypes extends Model
{
    public static $columns = ['id', 'name'];
    protected $fillable = ['id', 'name'];

    public function getAllProjectTypes() {
        return DB::table('project_types')->select('id', 'name')->get();
    }

    public function getPriorityById($id) {
        return DB::table('project_types')->select('id', 'name')
            ->where('id', $id)
            ->get();
    }
}
