<?php

namespace App;

use Ulid\Ulid;
use App\Traits\VueTable;
use App\Traits\LogActivity;
use Illuminate\Support\Str;
use App\Traits\Restrictable;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use LogActivity;
    use Restrictable;
    use VueTable;

    public static $columns = ['id', 'created_at', 'reference', 'amount', 'gateway', 'note', 'account.name'];

    protected $fillable = [
        'reference', 'amount', 'gateway', 'user_id', 'account_id', 'received', 'hash', 'note', 'file', 'invoice_id',
        'purchase_id', 'review', 'reviewed_by', 'account_transaction_id', 'gateway_transaction_id', 'payer_transaction_id',
    ];

    protected $hidden = ['updated_at'];

    protected $with = ['user', 'account', 'payable'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (!$model->reference) {
                $model->reference = Ulid::generate(true);
            }
        });

        static::created(function ($payment) {
            $payment->disableLogging();
            $payment->fill(['hash' => sha1(now() . Str::random())])->save();
        });
    }

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function invoices()
    {
        return $this->belongsToMany(Invoice::class);
    }

    public function payable()
    {
        return $this->morphTo();
    }

    public function purchases()
    {
        return $this->belongsToMany(Purchase::class);
    }

    public static function scopeDue($query)
    {
        return $query->where('received', '!=', 1);
    }

    public static function scopeOfCustomer($query, $customer_id)
    {
        return $query->where('payable_type', 'App\Customer')->where('payable_id', $customer_id);
    }

    public static function scopeOfVendor($query, $vendor_id)
    {
        return $query->where('payable_type', 'App\Vendor')->where('payable_id', $vendor_id);
    }

    public static function scopeReceived($query)
    {
        return $query->where('received', 1);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
