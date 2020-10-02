<?php

namespace App;

use App\Traits\VueTable;
use App\Traits\LogActivity;
use Illuminate\Database\Eloquent\Model;

class Tax extends Model
{
    use LogActivity, VueTable;

    public static $columns = ['id', 'code', 'name', 'rate', 'details', 'number', 'compound', 'recoverable', 'state', 'same'];

    protected $casts    = ['state' => 'boolean', 'same' => 'boolean', 'compound' => 'boolean', 'recoverable' => 'boolean'];
    protected $fillable = ['name', 'code', 'rate', 'details', 'number', 'compound', 'recoverable', 'state', 'same'];

    public function invoices()
    {
        return $this->morphedByMany(Invoice::class, 'taxable');
    }

    public function items()
    {
        return $this->morphedByMany(InvoiceItem::class, 'taxable');
    }

    public function products()
    {
        return $this->morphedByMany(Product::class, 'taxable');
    }

    public function purchases()
    {
        return $this->morphedByMany(Purchase::class, 'taxable');
    }
}
