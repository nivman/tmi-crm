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
