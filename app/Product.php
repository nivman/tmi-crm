<?php

namespace App;

use App\Http\ModelForm;
use App\Providers\AppServiceProvider;
use App\Traits\VueTable;
use App\Traits\LogActivity;
use App\Traits\AttributableModel;
use App\Traits\DynamicHiddenVisible;
use Illuminate\Database\Eloquent\Model;
use mysql_xdevapi\Collection;
use Rinvex\Attributes\Models\Attribute;
use Rinvex\Attributes\Models\Type\Varchar;
use Rinvex\Attributes\Support\RelationBuilder;

class Product extends ModelForm
{
    use AttributableModel, DynamicHiddenVisible, LogActivity, VueTable;

    public static $columns = ['id', 'code', 'name', 'price', 'categories.name', 'taxes.name'];

    protected $fillable = ['code', 'name', 'cost', 'price', 'details', 'image'];

    protected $with     = ['categories', 'taxes', 'stock'];

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($product) {
            $product->taxes()->detach();
            $product->categories()->detach();
            if ($product->stock) {
                $product->stock->delete();
            }
        });
    }

    public function categories()
    {
        return $this->morphToMany(Category::class, 'categorizable');
    }

    public function InvoiceItem()
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function scopeSearch($query, $search)
    {
        if (!empty($search)) {
            $query->where(function ($query) use ($search) {
                $query->where('name', 'LIKE', "%$search%")
                    ->orWhere('code', 'LIKE', "%$search%");
            });
        }
        return $query;
    }

    public function stock()
    {
        return $this->hasOne(Stock::class);
    }

    public function taxes()
    {

        return $this->morphToMany(Tax::class, 'taxable');
    }

//    public function eav()
//    {
//        Attribute::typeMap([
//            'varchar' => Varchar::class,
//        ]);
//        app('rinvex.attributes.entities')->push('entity');
//
//    }
}
