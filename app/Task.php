<?php

namespace App;

use App\Http\ModelForm;
use App\Traits\AccountingJournal;
use App\Traits\AttributableModel;
use App\Traits\LogActivity;
use App\Traits\Restrictable;
use App\Traits\VueTable;

class Task extends ModelForm
{
    use AttributableModel, LogActivity, Restrictable, VueTable;

    public static $columns = ['id', 'name', 'details', 'start_date', 'end_date'];
    protected $fillable = ['id', 'name', 'details', 'start_date', 'end_date','customer_id', 'priority_id', 'status_id'];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function priority()
    {
        return $this->belongsTo(TaskPriority::class);
    }

    public function status()
    {
        return $this->belongsTo(Status::class);
    }

    public function getPriority($priorityId)
    {
        return (new TaskPriority())->getPriorityById($priorityId);
    }

    public function getCustomer($customerId)
    {
        return (new Customer())->getCustomerById($customerId);
    }
}
