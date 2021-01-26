<?php


namespace App\Http;


use App\Status;
use Illuminate\Database\Eloquent\Model;

class ModelForm extends  Model
{
    protected $status;

    /**
     * @param mixed $status
     */
    public function setStatus($status): void
    {
        $this->status = $status;
    }

    /**
     * @param $statusId
     * @return mixed
     */
    public function getStatus($statusId)
    {
        $this->status = (new Status)->getStatusById($statusId);
        return $this->status;
    }

    public static function convertEntityName($data, $count)
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
                    case 'App\Expenses':
                        $value->entity_name = 'הוצאות';
                        break;
                    case 'App\UpSale':
                        $value->entity_name = '(Upselling) מכירות נוספות';
                        break;
                }
                return $value;
            },
            array_keys($data->data),
            array_values($data->data)
        );

        return json_encode(array('data' => $result, 'count' => $count));

    }
}