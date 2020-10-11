<?php

namespace App;

use App\Traits\LogActivity;
use App\Traits\VueTable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Status extends Model
{
    use LogActivity, VueTable;
    public static $columns = ['id', 'name', 'entity_name', 'color'];
    protected $fillable = ['id', 'name', 'entity_name', 'color'];
    public static function convertEntityName($data)
    {
        $result = array_map(
            function($key, $value) {
                $entityName = $value->entity_name;
                switch ($entityName) {
                    case 'App\Customer':
                        $value->entity_name = 'לקוח';
                        break;
                    case 'App\Task':
                        $value->entity_name = 'משימה';
                        break;
                    case 'App\Lead':
                        $value->entity_name = 'ליד';
                        break;
                }
                return $value;
            },
            array_keys($data->data),
            array_values($data->data)
        );

        return json_encode(array('data' => $result, 'count' => count($result)));

    }

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
