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
    use AccountingJournal, AttributableModel, LogActivity, Restrictable, VueTable;

    public static $columns = ['id', 'first_name', 'last_name', 'email', 'phone'];

    protected $fillable = ['first_name', 'last_name', 'email', 'phone', 'customer_id'];
    protected $hidden   = ['created_at', 'updated_at'];


    public function createNewContact($customer, $id)
    {
        $customerName =  explode(' ', $customer['name']);

        $firstName = $customerName[0];
        $lastName = count($customerName) > 1 ? $customerName[1] : ' ';

      $contact = $this::create([
            'first_name'=> $firstName,
            'last_name' => $lastName,
            'email' => $customer['email'],
            'phone' => $customer['phone'],
            'customer_id' => $id
        ]);
          return $contact;
    }

    public function getContactByCustomer($id)
    {
        return DB::table('contacts')->select('id', DB::raw('CONCAT(first_name, " ", last_name) As full_name'), 'customer_id')
            ->where('customer_id', $id)
            ->get();
    }

    public function scopeSearch($query, $search)
    {
        if (!empty($search)) {
            $query->where(function ($query) use ($search) {
                $query->where('first_name', 'LIKE', "%$search%")
                    ->orWhere('last_name', 'LIKE', "%$search%");
            });
        }
        return $query;
    }

    public function getContactById($id)
    {
        return DB::table('contacts')->select('customer_id')
            ->where('id',$id)
            ->get()->toArray()[0]->customer_id;
    }

    public function getContactByCustomerId($id)
    {

        $contacts =  DB::table('contacts')->select('*')
            ->where('customer_id', $id)
            ->get()->toArray();

        return Contact::hydrate($contacts);

    }
}
