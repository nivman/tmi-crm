<?php

namespace App;

use Ulid\Ulid;
use App\Traits\VueTable;
use App\Traits\LogActivity;
use App\Traits\Restrictable;
use App\Traits\AttributableModel;
use Illuminate\Database\Eloquent\Model;

class Recurring extends Model
{
    use AttributableModel, LogActivity, Restrictable, VueTable;

    public static $columns = ['id', 'reference', 'start_date', 'create_before', 'repeat', 'customer.name', 'active'];

    protected $casts = [
        'active'          => 'boolean',
        'start_date'      => 'date:Y-m-d',
        'last_created_at' => 'datetime:Y-m-d H:00',
    ];
    protected $fillable = [
        'reference', 'start_date', 'create_before', 'repeat', 'active',
        'last_created_at', 'customer_id', 'user_id', 'note', 'total', 'discount',
        'grand_total', 'product_tax_amount', 'order_tax_amount', 'total_tax_amount',
    ];
    protected $hidden = ['created_at', 'updated_at'];
    protected $with   = ['customer'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (!$model->reference) {
                $model->reference = Ulid::generate(true);
            }
        });

        static::deleting(function ($recurring) {
            $recurring->taxes()->detach();
            $recurring->items()->delete();
        });
    }

    public static function scopeActive($query)
    {
        $today = date('Y-m-d');
        return $query->where('active', 1)->where('start_date', '<=', $today);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function items()
    {
        return $this->hasMany(RecurringItem::class);
    }

    public function taxes()
    {
        return $this->morphToMany(Tax::class, 'taxable');
    }
}
