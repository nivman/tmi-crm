<?php

namespace App\Http\Controllers;

use App\Vendor;
use App\Payment;
use App\Customer;
use Illuminate\Http\Request;

class PaymentsController extends Controller
{
    protected function isValid($request)
    {
        return $request->validate([
            'note'        => 'nullable',
            'gateway'     => 'nullable',
            'received'    => 'nullable',
            'reference'   => 'nullable',
            'vendor_id'   => 'nullable',
            'invoice_id'  => 'nullable',
            'account_id'  => 'required',
            'customer_id' => 'nullable',
            'purchase_id' => 'nullable',
            'amount'      => 'required|numeric|min:1',
        ]);
    }

    protected function payer($v)
    {
        if (isset($v['customer_id']) && !empty($v['customer_id'])) {
            $payer = Customer::findOrFail($v['customer_id']);
        } else {
            $payer = Vendor::findOrFail($v['vendor_id']);
        }
        return $payer;
    }

    public function destroy(Payment $payment)
    {
        $payment->delete();
        return response(['success' => true], 204);
    }

    public function email(Payment $payment)
    {
        if ($payment->payable->email) {
          //  try {
                \Mail::to($payment->payable->email)->send(new \App\Mail\PaymentCreated($payment));
        //    } catch (\Exception $e) {
        //        \Log::error('Unable to send email, please check your system settings. Error: ' . $e->getMessage());
        //    }
            return response(['success' => true], 200);
        }
        return response(['message' => 'Payable (customer/vendor) does not have email address.'], 422);
    }

    public function index()
    {
        return response()->json(Payment::myPayments()->vueTable(Payment::$columns));
    }

    public function show(Payment $payment)
    {
        return $payment;
    }

    public function store(Request $request)
    {
        $v             = $this->isValid($request);
        $payer         = $this->payer($v);
        $v['user_id']  = auth()->id();
        $v['received'] = !!$v['vendor_id'] || $v['gateway'] == 'cash';
        return $payer->payments()->create($v);
    }

    public function update(Request $request, Payment $payment)
    {
        $v             = $this->isValid($request);
        $v['received'] = !!$v['vendor_id'] || $v['gateway'] == 'cash';
        $payment->update($v);
        $payer = $this->payer($v);
        $payer->payments()->save($payment);
        return $payment;
    }
}
