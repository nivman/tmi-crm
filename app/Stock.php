<?php

namespace App;

// use App\Traits\LogActivity;
use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    // use LogActivity;

    public $timestamps  = false;
    protected $fillable = ['company_id', 'product_id', 'qty'];
    protected $hidden   = ['company_id', 'product_id'];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
