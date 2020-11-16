<?php

namespace App\Http\Controllers;

use App\ArrivalSources;
use App\Contact;
use App\Customer;
use App\Events\StatusChange;
use App\Events\StatusChangeEvent;
use App\Status;
use Illuminate\Http\Request;
use App\Http\Requests\CustomerRequest;

class CustomersController extends Controller
{
    public function create()
    {
        $customerStatuses = (new Status)->getAllEntityStatus('App\Customer');
        $arrivalSources = ArrivalSources::all();

        $customer = new Customer;
        return ['attributes' => $customer->attributes(), 'statuses' => $customerStatuses, 'arrivalSources' =>$arrivalSources];
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

        $contacts = (new Contact)->getContactByCustomerId($customer->getQueueableId());
          foreach ($contacts->getIterator() as $contact){
              $contact->delete();
          }

        $customer->delete();
        return response(['success' => true], 204);
    }

    public function index(Request $request)
    {

        $leadRequest = strpos($request->getPathInfo(), 'lead') ? 1 : 0;

        $customers = Customer::with(['journal', 'status'])->where('is_lead','=',$leadRequest)->vueTable(Customer::$columns);
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
        return Customer::search($v['query'])->select(
            \DB::raw("*, id as value,
                if (`name` LIKE '{$v['query']}%', 20, if (`name` LIKE '%{$v['query']}%', 10, 0))
                + if (`company` LIKE '%{$v['query']}%', 5,  0)
                + if (`phone` LIKE '%{$v['query']}%', 4,  0)
                + if (`email` LIKE '%{$v['query']}%', 3,  0)
                as weight")
        )->orderBy('weight', 'desc')->limit(10)->get();

    }

    public function show(Customer $customer)
    {
        $customer->attributes = $customer->attributes();

        $customer->status = $customer->getStatus($customer->getAttribute('status_id'));
        $customer->arrivalSources = ArrivalSources::select('id','name','color')->find($customer->getAttribute('arrival_source_id'));

        $customerStatuses = (new Status)->getAllEntityStatus('App\Customer');
        $arrivalSources = ArrivalSources::all();
        $customer->load($customer->attributes->pluck('slug')->toArray());
        return ['customer' => $customer, 'statuses' => $customerStatuses, 'arrivalSources' => $arrivalSources];

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
        $v['arrival_source_id'] = $request->request->get('arrivalSource');
        $v['is_lead'] = $request->request->get('is_lead');
        $contact = new Contact();
        $customer = Customer::create($v);
        $this->setStatusHistory($v['status_id'], $customer->id);
        $contact->createNewContact($v, $customer->id);
        return $customer;
    }

    public function update (CustomerRequest $request, Customer $customer)
    {
        $v = $request->validated();

        $v['status_id'] = $request->request->get('status');
        $v['arrival_source_id'] = $request->request->get('arrivalSource');
        $v['is_lead'] = $request->request->get('is_lead');
        $customer->update($v);

        $this->setStatusHistory($v['status_id'], $customer->id);
        return $customer;
    }

    public function getCustomersByIds($ids)
    {
        return (new Customer)->getCustomersByIds($ids);
    }

    public function getCustomerById($id) {
        return Customer::find($id);
    }
    /**
     * @param $status_id
     * @param $customer_id
     */
    public function setStatusHistory($status_id, $customer_id): void
    {
        $status = (new Status)->getStatus($status_id);

        if ($status && count($status) > 0) {
            $status = $status->first();
            $status->setAttribute('entity_id', $customer_id);
            event(new StatusChangeEvent($status));
        }
    }

}
