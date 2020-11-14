<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class StatusHistory extends Model
{
    public static $columns = ['id', 'updated_at', 'change_time', 'status_id', 'entity_id', 'entity_type'];

    protected $fillable = ['id', 'updated_at', 'change_time', 'status_id', 'entity_id', 'entity_type'];


    public function getEntityStatusesHistory($entity_type, $entity_id)
    {
        $where = ['sh.entity_id' => $entity_id, 'sh.entity_type' => $entity_type];
        return DB::table('status_histories', 'sh')->select('sh.updated_at', 'statuses.name', 'statuses.color')
            ->join('statuses', 'statuses.id', '=', 'sh.status_id')
            ->where($where)
            ->get()->toArray();
    }
}
