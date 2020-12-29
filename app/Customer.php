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

    public static $columns = ['id', 'name', 'company', 'email', 'phone', 'address',  'created_at'];

    protected $fillable = ['name', 'company', 'email', 'phone', 'user_id', 'opening_balance', 'address', 'status_id', 'is_lead', 'arrival_source_id'];
    protected $hidden = ['updated_at'];

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

            $customer = Customer::find($customersId);
            $customerAttributeRelations = array_keys($customer->entityAttributeRelations);

            foreach ($customerAttributeRelations as $customerAttributeRelation) {

                $customFieldsSlugs = collect(CustomField::whereSlug($customerAttributeRelation));

                if (count($customFieldsSlugs->toArray()) > 0) {
                    $customFieldsName = json_decode($customFieldsSlugs->first()->name);

                    $attributesNames[$customFieldsName->en] = $customFieldsName->en;
                    $customer->$customerAttributeRelation;

                    if ($customer->getRelation($customerAttributeRelation)) {

                        $attribute = $customer->getRelation($customerAttributeRelation)->getAttributes();

                        $attribute['attributeName'] = $customFieldsName->en;

                    } else {
                        $attribute = ['attributeName' => $customFieldsName->en, 'content' => '', 'entity_id' => $customersId];
                    }
                    $attributes[] = $attribute;
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

        return DB::table('customers', 'cu')
            ->leftJoin('contacts', 'cu.id', '=', 'contacts.customer_id')
            ->select('cu.*', 'contacts.id as contact_id', 'contacts.first_name as contact_first_name', 'contacts.last_name as contact_last_name')
            ->whereIn('cu.id', $customersIds)
            ->get();
    }

    public function getUnseenLeads()
    {
        return DB::table('customers', 'cu')
            ->select('cu.*')
            ->where(['cu.is_lead'=> 1, 'opening_balance' => -1])
            ->get()->toArray();
    }

    public function getProfits($customers)
    {

        $customersIds = array_column($customers['data'],'id');
        $customersIds = implode(',', $customersIds);
        return  (new Project())->sumTasksTimeByCustomersId($customersIds);

    }
}
