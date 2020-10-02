<?php

namespace App\Http\Controllers;

use App\Vendor;
use Illuminate\Http\Request;
use App\Http\Requests\VendorRequest;

class VendorsController extends Controller
{
    public function create()
    {
        $vendor = new Vendor;
        return $vendor->attributes();
    }

    public function destroy(Vendor $vendor)
    {
        if ($vendor->purchases()->exists()) {
            return response(['message' => 'Vendor has been attached to some purchases and can not be deleted.'], 422);
        } elseif ($vendor->payments()->exists()) {
            return response(['message' => 'Vendor has been attached to some payments and can not be deleted.'], 422);
        } elseif ($vendor->users()->exists()) {
            return response(['message' => 'Vendor has been attached to some users and can not be deleted.'], 422);
        }
        $vendor->delete();
        return response(['success' => true], 204);
    }

    public function index()
    {
        return response()->json(Vendor::with('journal')->mine()->vueTable(Vendor::$columns));
    }

    public function search(Request $request)
    {
        $v       = $request->validate(['query' => 'required|string']);
        $results = Vendor::search($v['query'])->select(
            \DB::raw("*, id as value,
                if (`name` LIKE '{$v['query']}%', 20, if (`name` LIKE '%{$v['query']}%', 10, 0))
                + if (`company` LIKE '%{$v['query']}%', 5,  0)
                + if (`phone` LIKE '%{$v['query']}%', 4,  0)
                + if (`email` LIKE '%{$v['query']}%', 3,  0)
                as weight")
        )->orderBy('weight', 'desc')->limit(10)->get();
        return $results;
    }

    public function show(Vendor $vendor)
    {
        $vendor->attributes = $vendor->attributes();
        $vendor->load($vendor->attributes->pluck('slug')->toArray());
        return $vendor;
    }

    public function store(VendorRequest $request)
    {
        $v            = $request->validated();
        $v['user_id'] = auth()->id();
        return Vendor::create($v);
    }

    public function update(VendorRequest $request, Vendor $vendor)
    {
        $user = auth()->user();
        if ($user->hasRole(['staff', 'customer', 'vendor']) && $user->vendor_id != $vendor->id) {
            abort(403);
        }
        $v = $request->validated();
        $vendor->update($v);
        return $vendor;
    }
}
