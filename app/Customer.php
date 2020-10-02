<?php

namespace App;

use App\Traits\VueTable;
use App\Traits\LogActivity;
use App\Traits\Restrictable;
use App\Traits\AccountingJournal;
use App\Traits\AttributableModel;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use AccountingJournal, AttributableModel, LogActivity, Restrictable, VueTable;

    public static $columns = ['id', 'name', 'company', 'email', 'phone', 'address', 'state', 'country'];

    protected $fillable = ['name', 'company', 'email', 'phone', 'user_id', 'opening_balance', 'address', 'state', 'country', 'state_name', 'country_name'];
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
}
