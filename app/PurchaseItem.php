<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PurchaseItem extends Model
{
    protected $fillable = [
        'name', 'code', 'qty', 'cost', 'unit_cost', 'subtotal', 'item_taxes',
        'product_id', 'tax_amount', 'total_tax_amount', 'net_cost', 'discount',
        'discount_amount', 'total_discount_amount',
    ];
    protected $hidden = ['created_at', 'updated_at'];
    protected $with   = ['taxes'];

    public function getItemTaxesAttribute($value)
    {
        return json_decode($value);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }

    public function setItemTaxesAttribute($value)
    {
        $this->attributes['item_taxes'] = json_encode($value);
    }

    public function stock()
    {
        return $this->hasOne(Stock::class, 'product_id', 'product_id');
    }

    public function taxes()
    {
        return $this->morphToMany(Tax::class, 'taxable')->withPivot(['amount', 'total_amount']);
    }
}
