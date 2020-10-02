<?php

namespace App\Http\Controllers;

use App\Purchase;
use App\Helpers\Order;
use App\Http\Requests\PurchaseRequest;

class PurchasesController extends Controller
{
    public function create()
    {
        $purchase = new Purchase;
        return $purchase->attributes();
    }

    public function destroy(Purchase $purchase)
    {
        $purchase->delete();
        return response(['success' => true], 204);
    }

    public function email(Purchase $purchase)
    {
        if ($purchase->vendor->email) {
            try {
                \Mail::to($purchase->vendor->email)->send(new \App\Mail\PurchaseCreated($purchase));
            } catch (\Exception $e) {
                \Log::error('Unable to send email, please check your system settings. Error: ' . $e->getMessage());
            }
            return response(['success' => true], 200);
        }
        return response(['message' => 'Vendor does not have email address.'], 422);
    }

    public function index()
    {
        return response()->json(purchase::myPurchases()->vueTable(purchase::$columns));
    }

    public function payments(Purchase $purchase)
    {
        return $purchase->payments;
    }

    public function show(Purchase $purchase)
    {
        $purchase->attributes = $purchase->attributes();
        $purchase->load($purchase->attributes->pluck('slug')->toArray());
        $purchase->load('items', 'items.product', 'taxes');
        return $purchase;
    }

    public function store(PurchaseRequest $request)
    {
        $v = Order::prepareData($request->validated(), $request, true, true);
        return Order::orderTransaction($v, 'App\Purchase');
    }

    public function update(PurchaseRequest $request, Purchase $purchase)
    {
        $v = Order::prepareData($request->validated(), $request, false, true);
        return Order::orderTransaction($v, 'App\Purchase', $purchase);
    }
}
