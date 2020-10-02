<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RecurringItem extends Model
{
    protected $fillable = [
        'name', 'code', 'qty', 'price', 'unit_price', 'subtotal', 'item_taxes',
        'product_id', 'tax_amount', 'total_tax_amount', 'net_price', 'discount',
        'discount_amount', 'total_discount_amount',
    ];
    protected $hidden = ['created_at', 'updated_at'];
    protected $with   = ['taxes'];

    protected static function boot()
    {
        parent::boot();
        static::deleting(function ($item) {
            $item->taxes()->detach();
        });
    }

    public function getItemTaxesAttribute($value)
    {
        return json_decode($value);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function recurring()
    {
        return $this->belongsTo(Recurring::class);
    }

    public function setItemTaxesAttribute($value)
    {
        $this->attributes['item_taxes'] = json_encode($value);
    }

    public function taxes()
    {
        return $this->morphToMany(Tax::class, 'taxable');
    }
}
