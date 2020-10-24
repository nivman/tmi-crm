<?php

namespace App\Http\Controllers;

use App\Contact;
use App\Customer;
use App\Status;
use Illuminate\Http\Request;
use App\Http\Requests\CustomerRequest;

class ContactsController extends Controller
{
    public function create()
    {

    }

    public function destroy(Customer $customer)
    {

    }

    public function getContactByCustomerId($id)
    {
        return (new Contact)->getContactByCustomer($id);
    }

    public function index()
    {

    }

    public function search(Request $request)
    {
        $v = $request->validate(['query' => 'required|string']);

        return Contact::search($v['query'])->select(
            \DB::raw("*, id as value,
                if (`first_name` LIKE '{$v['query']}%', 20, if (`first_name` LIKE '%{$v['query']}%', 10, 0))
                + if (`last_name` LIKE '%{$v['query']}%', 5,  0)
                as weight"),
            \DB::raw( 'CONCAT(first_Name, " ", last_Name) As full_name')
        )->orderBy('weight', 'desc')->limit(10)->get();

    }

    public function show(Customer $customer)
    {

    }

    public function store(CustomerRequest $request)
    {

    }

    public function update(CustomerRequest $request, Customer $customer)
    {

    }
}
