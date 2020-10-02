<?php

namespace App;

use Ulid\Ulid;
use App\Traits\VueTable;
use App\Traits\LogActivity;
use Illuminate\Support\Str;
use App\Traits\Restrictable;
use App\Traits\AttributableModel;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use AttributableModel;
    use LogActivity;
    use Restrictable;
    use VueTable;

    public static $columns = ['id', 'date', 'reference', 'customer.name', 'draft'];

    protected $casts = ['draft' => 'boolean'];

    protected $fillable = [
        'date', 'reference', 'hash', 'draft', 'user_id', 'paid', 'note',
        'customer_id', 'total', 'grand_total', 'all_taxes', 'recurring_id',
        'discount', 'product_tax_amount', 'order_tax_amount', 'total_tax_amount',
    ];

    protected $hidden = ['updated_at'];

    protected $with = ['customer', 'taxes'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (!$model->reference) {
                $model->reference = Ulid::generate(true);
            }
        });

        static::created(function ($invoice) {
            $invoice->disableLogging();
            $invoice->fill(['hash' => sha1(now() . Str::random())])->save();
        });

        static::deleting(function ($invoice) {
            $invoice->taxes()->detach();
            $invoice->items()->delete();
        });
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function payments()
    {
        return $this->belongsToMany(Payment::class)->withPivot('amount');
    }

    public function routeNotificationForMail($notification)
    {
        return $this->customer->email;
    }

    public static function scopeActive($query)
    {
        return $query->whereDraft('0')->orWhereNull('draft');
    }

    public static function scopeOfCustomer($query, $customer_id)
    {
        return $query->where('customer_id', $customer_id);
    }

    public static function scopePaid($query)
    {
        return $query->where('paid', 1);
    }

    public static function scopeUnpaid($query)
    {
        return $query->wherePaid('0')->orWhereNull('paid');
    }

    public function taxes()
    {
        return $this->morphToMany(Tax::class, 'taxable')->withPivot('total_amount');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
