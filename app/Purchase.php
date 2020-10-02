<?php

namespace App;

use Ulid\Ulid;
use App\Traits\VueTable;
use App\Traits\LogActivity;
use Illuminate\Support\Str;
use App\Traits\Restrictable;
use App\Traits\AttributableModel;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use AttributableModel;
    use LogActivity;
    use Restrictable;
    use VueTable;

    public static $columns = ['id', 'date', 'reference', 'vendor.name', 'draft'];

    protected $casts = ['draft' => 'boolean'];

    protected $fillable = [
        'date', 'reference', 'hash', 'draft', 'user_id', 'paid',
        'vendor_id', 'note', 'total', 'grand_total', 'all_taxes',
        'discount', 'product_tax_amount', 'order_tax_amount', 'total_tax_amount',
    ];

    protected $hidden = ['created_at', 'updated_at'];

    protected $with = ['vendor'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (!$model->reference) {
                $model->reference = Ulid::generate(true);
            }
        });

        static::created(function ($purchase) {
            $purchase->disableLogging();
            $purchase->fill(['hash' => sha1(now() . Str::random())])->save();
        });

        static::deleting(function ($purchase) {
            $purchase->taxes()->detach();
            $purchase->items()->delete();
        });
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function items()
    {
        return $this->hasMany(PurchaseItem::class);
    }

    public function payments()
    {
        return $this->belongsToMany(Payment::class)->withPivot('amount');
    }

    public static function scopeActive($query)
    {
        return $query->whereDraft('0')->orWhereNull('draft');
    }

    public static function scopeOfVendor($query, $vendor_id)
    {
        return $query->where('vendor_id', $vendor_id);
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
        return $this->morphToMany(Tax::class, 'taxable');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }
}
