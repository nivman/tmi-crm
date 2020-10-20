<?php

namespace App;

use App\Http\ModelForm;
use App\Traits\LogActivity;
use App\Traits\VueTable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Status extends ModelForm
{
    use LogActivity, VueTable;
    public static $columns = ['id', 'name', 'entity_name', 'color'];
    protected $fillable = ['id', 'name', 'entity_name', 'color'];

    public function getAllEntityStatus($entityName) {

        return DB::table('statuses')->select('id', 'name', 'color')
            ->where('entity_name', $entityName)
            ->get();

    }

    public function getStatusById($id) {
        return DB::table('statuses')->select('id', 'name', 'color')
            ->where('id', $id)
            ->get();
    }
}
