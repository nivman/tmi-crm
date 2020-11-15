<?php

namespace App\Http\Controllers;

use App\Contact;
use App\Customer;
use App\Http\Requests\ContactRequest;
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

    public function show(Contact $contact)
    {

        $customer = Customer::find($contact->customer_id);

        return ['contact' =>$contact, 'customer' => $customer];
    }

    public function store(ContactRequest  $request)
    {
        $v = $request->validated();
        $v['customer_id'] = $request->request->get('customer_id');
        $contact =  new Contact();
        Contact::create($v);
        return $contact;
    }

    public function update(ContactRequest $request, Contact $contact)
    {
        $v = $request->validated();
        $v['customer_id'] = $request->request->get('customer_id');
        $contact->update($v);
        return $contact;
    }

    public function getContactsDetailsByCustomerId($customerId)
    {
        return (new Contact)->getContactByCustomerId($customerId);
    }
}
