<?php

namespace App\Http\Controllers;

use App\Product;
use Illuminate\Http\Request;
use App\Http\Requests\ProductRequest;
use Illuminate\Support\Facades\Storage;

class ProductsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function create()
    {
        $product = new Product;
        return $product->attributes();
    }

    public function destroy(Product $product)
    {
        if ($product->invoiceItem()->exists()) {
            return response(['success' => false, 'message' => 'Products can\'t be deleted as it has invoice data attached.'], 422);
        }
        $product->delete();
        return response(['success' => true], 204);
    }

    public function index(Request $request)
    {

        if (!auth()->user()->hasRole(['admin', 'super'])) {
            Product::setStaticHidden(['cost']);
        }

        return response()->json(Product::vueTable(Product::$columns));
    }

    public function search(Request $request)
    {
        $v       = $request->validate(['query' => 'required|string']);
        $results = Product::search($v['query'])->select(
            \DB::raw("*, id as value,
                if (`name` LIKE '{$v['query']}%', 20, if (`name` LIKE '%{$v['query']}%', 10, 0))
                + if (`code` LIKE '%{$v['query']}%', 5,  0)
                + if (`details` LIKE '%{$v['query']}%', 1,  0)
                as weight")
        )->orderBy('weight', 'desc')->limit(10)->get();
        return $results;
    }

    public function show(Product $product)
    {
        if (!auth()->user()->hasRole(['admin', 'super'])) {
            Product::setStaticHidden(['cost']);
        }
        $product->attributes = $product->attributes();
        $product->load($product->attributes->pluck('slug')->toArray());
        return $product;
    }

    public function store(ProductRequest $request)
    {
        $v = $request->validated();
        if ($request->has('image')) {
            $path       = $request->image->store('/images', 'public');
            $v['image'] = Storage::disk('public')->url($path);
        }
        $product = Product::create($v);
        $product->taxes()->sync($v['taxes']);
        $product->categories()->sync($v['category']);
        $product->stock()->create(['company_id' => 1, 'qty' => !empty($v['qty']) ? $v['qty'] : 0]);
        $product->load('categories', 'taxes', 'stock');
        return $product;
    }

    public function update(ProductRequest $request, Product $product)
    {
        $v = $request->validated();
        if ($request->has('image')) {
            $path       = $request->image->store('/images', 'public');
            $v['image'] = Storage::disk('public')->url($path);
        }
        $product->fill($v)->save();
        $product->taxes()->sync($v['taxes']);
        $product->categories()->sync($v['category']);
        return $product;
    }
}
