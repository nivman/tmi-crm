<?php

namespace App;

use App\Http\ModelForm;
use App\Traits\VueTable;
use App\Traits\LogActivity;
use App\Traits\Restrictable;
use App\Traits\AccountingJournal;
use App\Traits\AttributableModel;
use Illuminate\Support\Facades\DB;


class Customer extends ModelForm
{
    use AccountingJournal, AttributableModel, LogActivity, Restrictable, VueTable;

    public static $columns = ['id', 'name', 'company', 'email', 'phone', 'address', 'state', 'country'];

    protected $fillable = ['name', 'company', 'email', 'phone', 'user_id', 'opening_balance', 'address', 'state', 'country', 'state_name', 'country_name','status_id'];
    protected $hidden   = ['created_at', 'updated_at'];

    protected static function boot()
    {
        parent::boot();

        static::created(function ($customer) {

            $customer->initJournal();
            if ($ob = $customer->opening_balance) {
                $customer->refresh()->journal->creditDollars($ob, 'opening_balance');
            }


        });
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function payments()
    {
        return $this->morphMany(Payment::class, 'payable');
    }

    public function scopeSearch($query, $search)
    {
        if (!empty($search)) {
            $query->where(function ($query) use ($search) {
                $query->where('name', 'LIKE', "%$search%")
                    ->orWhere('phone', 'LIKE', "%$search%")
                    ->orWhere('email', 'LIKE', "%$search%")
                    ->orWhere('company', 'LIKE', "%$search%");
            });
        }
        return $query;
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function status()
    {
        return $this->belongsTo(Status::class);
    }

    public function getCustomFields($customers)
    {
        $attributes = [];
        $attributesNames = [];
        $customersIds = array_column($customers["data"], 'id');

        foreach ($customersIds as $key => $customersId) {
            /** @var Customer $customer */
            $customer = Customer::find($customersId);
            $customerAttributeRelations = array_keys($customer->entityAttributeRelations);

            foreach ($customerAttributeRelations as $customerAttributeRelation) {

                $customFieldsSlugs = collect(CustomField::whereSlug($customerAttributeRelation));
                $customFieldsName = json_decode($customFieldsSlugs->first()->name);

                $attributesNames[$customFieldsName->en] = $customFieldsName->en;
                $customer->$customerAttributeRelation;

                if ($customer->getRelations()[$customerAttributeRelation]) {

                    $attribute = $customer->getRelations()[$customerAttributeRelation]->getAttributes();

                    $customFieldsSlugs = collect(CustomField::find($attribute['attribute_id']-1)->pluck('name')->get($attribute['attribute_id']-1));

                    $attribute['attributeName'] = $customFieldsSlugs->first();

                    $attributes[] = $attribute;

                    $customer->customerField = [$attribute['attributeName'] => $attribute['content']];
                }
            }
        }

       return ['attributes' => $attributes, 'attributesNames' => $attributesNames];
   }

    public function getContactByCustomer($id)
    {
        $customerId = (new Contact)->getContactById($id);
        return Customer::find($customerId);
    }

    public function getCustomerById($id)
    {
        return Customer::find($id);
    }

    public function getCustomersByIds($ids)
    {
        $customersIds = explode(',', $ids);

       return DB::table('customers','cu')
            ->leftJoin('contacts', 'cu.id', '=', 'contacts.customer_id')
            ->select('cu.*', 'contacts.id as contact_id', 'contacts.first_name as contact_first_name', 'contacts.last_name as contact_last_name')
            ->whereIn('cu.id', $customersIds)
            ->get();
    }
}
