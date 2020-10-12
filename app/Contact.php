<?php

namespace App;

use App\Traits\VueTable;
use App\Traits\LogActivity;
use App\Traits\Restrictable;
use App\Traits\AccountingJournal;
use App\Traits\AttributableModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Contact extends Model
{
    use LogActivity, Restrictable, VueTable;

    public static $columns = ['id', 'first_name', 'last_name', 'email', 'phone'];

    protected $fillable = ['first_name', 'last_name', 'email', 'phone', 'customer_id'];
    protected $hidden   = ['created_at', 'updated_at'];


    public function createNewContact($customer, $id)
    {
        $customerName =  explode(' ', $customer['name']);

        $firstName = $customerName[0];
        $lastName = count($customerName) > 1 ? $customerName[1] : '';

        $this::create([
            'first_name'=> $firstName,
            'last_name' => $lastName,
            'email' => $customer['email'],
            'phone' => $customer['phone'],
            'customer_id' => $id
        ]);

    }

    public function getContactByCustomer($id)
    {
        return DB::table('contacts')->select('id', 'first_name', 'last_name')
            ->where('customer_id', $id)
            ->get();
    }
}
