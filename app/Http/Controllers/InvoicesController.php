<?php

namespace App\Http\Controllers;

use App\Invoice;
use App\Helpers\Order;
use App\Http\Requests\InvoiceRequest;

class InvoicesController extends Controller
{
    protected function createPayment(Invoice $invoice, $request)
    {
        if ($request->create_payment) {
            $invoice->customer->payments()->create([
                'received'   => false,
                'user_id'    => auth()->id(),
                'invoice_id' => $invoice->id,
                'amount'     => $invoice->grand_total,
                'account_id' => env('DEFAULT_ACCOUNT'),
                'note'       => 'This is auto generated payment request for the invoice number ' . $invoice->id,
            ]);
        }
    }

    public function create()
    {
        $invoice = new Invoice;
        return $invoice->attributes();
    }

    public function destroy(Invoice $invoice)
    {
        $invoice->delete();
        return response(['success' => true], 204);
    }

    public function email(Invoice $invoice)
    {
        if ($invoice->customer->email) {
            try {
                \Mail::to($invoice->customer->email)->send(new \App\Mail\InvoiceCreated($invoice));
            } catch (\Exception $e) {
                \Log::error('Unable to send email, please check your system settings. Error: ' . $e->getMessage());
            }
            return response(['success' => true], 200);
        }
        return response(['message' => 'Customer does not have email address.'], 422);
    }

    public function index()
    {
        return response()->json(Invoice::myInvoices()->vueTable(Invoice::$columns));
    }

    public function payments(Invoice $invoice)
    {
        return $invoice->payments;
    }

    public function show(Invoice $invoice)
    {
        $invoice->load('items', 'items.product', 'taxes', 'customer');
        $invoice->attributes = $invoice->attributes();
        $invoice->load($invoice->attributes->pluck('slug')->toArray());
        $invoice->customer->attributes = $invoice->customer->attributes();
        $invoice->customer->load($invoice->customer->attributes->pluck('slug')->toArray());
        return $invoice;
    }

    public function store(InvoiceRequest $request)
    {
        $v       = Order::prepareData($request->validated(), $request, true);
        $invoice = Order::orderTransaction($v, 'App\Invoice');
        $this->createPayment($invoice, $request);
        return $invoice;
    }

    public function update(InvoiceRequest $request, Invoice $invoice)
    {
        $v = Order::prepareData($request->validated(), $request);
        Order::orderTransaction($v, 'App\Invoice', $invoice);
        $invoice->refresh();
        $this->createPayment($invoice, $request);
        return $invoice;
    }
}
