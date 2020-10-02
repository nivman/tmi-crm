<?php

namespace App;

use App\Traits\VueTable;
use App\Traits\LogActivity;
use App\Traits\Restrictable;
use App\Traits\AccountingJournal;
use App\Traits\AttributableModel;
use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
    use AccountingJournal, AttributableModel, LogActivity, Restrictable, VueTable;

    public static $columns = ['id', 'name', 'company', 'email', 'phone', 'address', 'state', 'country'];

    protected $fillable = ['name', 'company', 'email', 'phone', 'user_id', 'opening_balance', 'address', 'state', 'country', 'state_name', 'country_name'];
    protected $hidden   = ['created_at', 'updated_at'];

    protected static function boot()
    {
        parent::boot();

        static::created(function ($vendor) {
            $vendor->initJournal();
            if ($vendor->opening_balance) {
                $vendor->refresh()->journal->creditDollars($vendor->opening_balance, 'opening_balance');
            }
        });
    }

    public static function scopeOfVendor($query, $vendor_id)
    {
        return $query->where('vendor_id', $vendor_id);
    }

    public function payments()
    {
        return $this->morphMany(Payment::class, 'payable');
    }

    public function purchases()
    {
        return $this->hasMany(Purchase::class);
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
