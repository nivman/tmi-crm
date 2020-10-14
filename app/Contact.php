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
        return DB::table('contacts')->select('id', DB::raw('CONCAT(first_Name, " ", last_Name) As full_name'))
            ->where('customer_id', $id)
            ->get();
    }

    public function getContactByName($search)
    {
        return DB::table('contacts')->select('id', DB::raw('CONCAT(first_Name, " ", last_Name) As full_name'))
            ->where('first_name','LIKE', '%'.$search.'%')
            ->orWhere('last_name','LIKE', '%'.$search.'%')
            ->get();
    }

    public function getContactById($id)
    {
        return DB::table('contacts')->select('customer_id')
            ->where('id',$id)
            ->get()->toArray()[0]->customer_id;
    }
}
