<?php

namespace App\Http\Controllers;

use App\Contact;
use App\Customer;
use App\Status;
use Illuminate\Http\Request;
use App\Http\Requests\CustomerRequest;

class CustomersController extends Controller
{
    public function create()
    {
        $customerStatuses = (new Status)->getAllEntityStatus('App\Customer');

        $customer = new Customer;
        return ['attributes' => $customer->attributes(), 'statuses' => $customerStatuses];
    }

    public function destroy(Customer $customer)
    {
        if ($customer->invoices()->exists()) {
            return response(['message' => 'Customer has been attached to some invoices and can not be deleted.'], 422);
        } elseif ($customer->payments()->exists()) {
            return response(['message' => 'Customer has been attached to some payments and can not be deleted.'], 422);
        } elseif ($customer->users()->exists()) {
            return response(['message' => 'Customer has been attached to some users and can not be deleted.'], 422);
        }
        $customer->delete();
        return response(['success' => true], 204);
    }

    public function index()
    {

        $customers = Customer::with(['journal', 'status'])->mine()->vueTable(Customer::$columns);
        $attributes = (new Customer)->getCustomFields($customers);

        foreach ($customers['data'] as $key => $customer) {

            foreach ($attributes['attributes'] as $item) {

                if (isset($item['entity_id']) && $item['entity_id'] == $customer['id']) {

                    $customer['custom'][$item['attributeName']] = $item['content'];
                    $customers['data'][$key] = $customer;
                }
            }

        }

        $customers['attributesNames'] = $attributes['attributesNames'];

        return response()->json($customers);
    }

    public function search(Request $request)
    {

        $v = $request->validate(['query' => 'required|string']);
        $results = Customer::search($v['query'])->select(
            \DB::raw("*, id as value,
                if (`name` LIKE '{$v['query']}%', 20, if (`name` LIKE '%{$v['query']}%', 10, 0))
                + if (`company` LIKE '%{$v['query']}%', 5,  0)
                + if (`phone` LIKE '%{$v['query']}%', 4,  0)
                + if (`email` LIKE '%{$v['query']}%', 3,  0)
                as weight")
        )->orderBy('weight', 'desc')->limit(10)->get();
        return $results;
    }

    public function show(Customer $customer)
    {
        $customer->attributes = $customer->attributes();

        $customer->status = $customer->getStatus($customer->getAttribute('status_id'));
        $customerStatuses = (new Status)->getAllEntityStatus('App\Customer');

        $customer->load($customer->attributes->pluck('slug')->toArray());
        return ['customer' => $customer, 'statuses' => $customerStatuses];

    }

    public function getCustomerByContactId($contactId)
    {
        $customer = (new Customer())->getContactByCustomer($contactId);
        if ($customer) {

            $customer->attributes = $customer->attributes();
            $customer->status = $customer->getStatus($customer->getAttribute('status_id'));
            $customer->load($customer->attributes->pluck('slug')->toArray());
        }
        $customerStatuses = (new Status)->getAllEntityStatus('App\Customer');
        return ['customer' => $customer, 'statuses' => $customerStatuses];
    }


    public function store(CustomerRequest $request)
    {

        $v = $request->validated();
        $v['user_id'] = auth()->id();
        $v['status_id'] = $request->request->get('status');
        $contact = new Contact();
        $customer = Customer::create($v);
        $contact->createNewContact($v, $customer->id);
        return $customer;
    }

    public function update(CustomerRequest $request, Customer $customer)
    {
        $user = auth()->user();
        if ($user->hasRole(['staff', 'customer', 'vendor']) && $user->customer_id != $customer->id) {
            abort(403);
        }
        $v = $request->validated();
        $v['status_id'] = $request->request->get('status');
        $customer->update($v);
        return $customer;
    }

}
