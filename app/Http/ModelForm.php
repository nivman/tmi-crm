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
}