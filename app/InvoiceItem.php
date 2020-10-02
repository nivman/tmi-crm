<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InvoiceItem extends Model
{
    protected $fillable = [
        'name', 'code', 'qty', 'price', 'unit_price', 'subtotal', 'item_taxes',
        'product_id', 'tax_amount', 'total_tax_amount', 'net_price', 'discount',
        'discount_amount', 'total_discount_amount',
    ];
    protected $hidden = ['created_at', 'updated_at'];
    protected $with   = ['stock', 'taxes'];

    public function getItemTaxesAttribute($value)
    {
        return json_decode($value);
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
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
