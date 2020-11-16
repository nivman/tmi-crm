<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Log;

class PublicController extends Controller
{
    public function completed(Request $request, $hash)
    {
        $payment = \App\Payment::where('hash', $hash)->first();

        if (!empty($payment)) {
            $paypal   = new \App\Helpers\PayPal;
            $response = $paypal->complete([
                'transactionId' => $payment->id,
                'amount'        => $paypal->formatAmount($payment->amount),
                'currency'      => env('CURRENCY_CODE'),
                'cancelUrl'     => $paypal->getCancelUrl($hash),
                'returnUrl'     => $paypal->getReturnUrl($hash),
            ]);

            if ($response->isSuccessful()) {
                $payment->update(['gateway' => 'PayPal_Express', 'gateway_transaction_id' => $response->getTransactionReference(), 'received' => 1]);
                if (auth()->guest()) {
                    return redirect('/message')->with('message', 'Thank you! your payment has been processed successfully.');
                }
                return redirect('/view/payment/' . $payment->hash)
                    ->with('message', 'Thank you! your payment has been processed successfully.');
            }

            if (auth()->guest()) {
                return redirect('/message')->with(['message' => $response->getMessage()]);
            }
            return redirect('/view/payment/' . $payment->hash)->with(['message' => $response->getMessage()]);
        }
        abort(404);
    }

    public function index(Request $request, $view, $hash)
    {
        if (auth()->guest() && !$request->hasValidSignature()) {
            abort(404);
        }

        if ($view == 'invoice') {
            $invoice = \App\Invoice::where('hash', $hash)->first();
            if (!empty($invoice)) {
                $invoice->load('items', 'taxes');
                $invoice->attributes = $invoice->attributes();
                $invoice->load($invoice->attributes->pluck('slug')->toArray());
                $invoice->customer->attributes = $invoice->customer->attributes();
                $invoice->customer->load($invoice->customer->attributes->pluck('slug')->toArray());
                return view('invoices.' . $invoice->company->template, compact('invoice', 'request'));
            }
        } elseif ($view == 'purchase') {
            $purchase = \App\Purchase::where('hash', $hash)->first();
            if (!empty($purchase)) {
                $purchase->load('items', 'taxes');
                $purchase->attributes = $purchase->attributes();
                $purchase->load($purchase->attributes->pluck('slug')->toArray());
                $purchase->vendor->attributes = $purchase->vendor->attributes();
                $purchase->vendor->load($purchase->vendor->attributes->pluck('slug')->toArray());
                return view('purchases.' . $purchase->company->template, compact('purchase', 'request'));
            }
        } elseif ($view == 'payment') {
            $payment = \App\Payment::where('hash', $hash)->first();
            if (!empty($payment)) {
                $accounts = \App\Account::offline()->get();

                $customer = $payment->payable instanceof \App\Customer;

                $payment->payable->attributes = $payment->payable->attributes();
                $payment->payable->load($payment->payable->attributes->pluck('slug')->toArray());
                return view('payments.' . $payment->company->template, compact('payment', 'accounts', 'request', 'customer'));
            }
        }

        // abort(404);
        return redirect()->to('/');
    }

    public function pay(Request $request, $gateway, $hash)
    {
        $payment = \App\Payment::where('hash', $hash)->first();
        if (!empty($payment)) {
            if ($gateway == 'offline') {
                if ($request->approve && $payment->gateway == 'offline' && auth()->check()) {
                    $user = auth()->user();
                    if ($user->hasRole(['super', 'admin', 'staff'])) {
                        $payment->update(['review' => null, 'reviewed_by' => $user->id, 'received' => 1]);
                        return response(['success' => true], 200);
                    }
                    return response(['success' => false], 403);
                }
                $v = $request->validate([
                    'file'    => 'required|file',
                    'account' => 'required|exists:accounts,id',
                ]);

                if ($request->file->isValid()) {
                    $ext  = $request->file->extension();
                    $name = $payment->hash . '.' . $ext;
                    $request->file->storeAs('payments', $name);
                    $payment->update(['account_id' => $v['account'], 'gateway' => 'offline', 'file' => $name, 'review' => 1]);
                    return redirect('/view/payment/' . $payment->hash)
                            ->with('message', 'Thank you for uploading, we will review and update the payments accordingly.');
                }
                return redirect('/view/payment/' . $payment->hash)
                            ->with('error', 'An error has been occurred, please try again.');
            }
            $card_gateway  = env('CARD_GATEWAY');
            $currency_code = env('CURRENCY_CODE');
            $omni          = new \App\Helpers\Payment($card_gateway);

            try {
                if ($card_gateway == 'Stripe') {
                    $response = $omni->purchase([
                        'amount'   => $payment->amount,
                        'currency' => $currency_code,
                        'token'    => $request->stripeToken,
                    ]);
                } else {
                    $card     = $request->only(['firstName', 'lastName', 'number', 'expiryMonth', 'expiryYear', 'cvv', 'billingAddress1', 'billingCity', 'billingPostcode', 'billingState', 'billingCountry']);
                    $response = $omni->purchase([
                        'amount'      => $payment->amount,
                        'currency'    => $currency_code,
                        'description' => 'Payment number ' . $payment->id,
                        'card'        => $card,
                    ]);
                }

                if ($response->isRedirect()) {
                    $response->redirect();
                } elseif ($response->isSuccessful()) {
                    $payment->update(['gateway' => $card_gateway, 'gateway_transaction_id' => $response->getTransactionReference(), 'received' => 1]);
                    if (auth()->guest()) {
                        return redirect('/message')->with('message', 'Thank you! your payment has been processed successfully.');
                    }
                    return redirect('/view/payment/' . $payment->hash)
                            ->with('message', 'Thank you! your payment has been processed successfully.');
                } else {
                    Log::error(print_r($response, true));
                    if (auth()->guest()) {
                        return redirect('/message')->with('error', $response->getMessage());
                    }
                    return redirect('/view/payment/' . $payment->hash)
                            ->with('error', $response->getMessage());
                }
            } catch (\Exception $e) {
                Log::error($e->getMessage());
                abort(500, 'Sorry, there was an error processing your payment. Please try again later.');
            }
        }
        abort(400);
    }

    public function paypal(Request $request, $hash)
    {
        $payment = \App\Payment::where('hash', $hash)->first();

        if (!empty($payment)) {
            $paypal   = new \App\Helpers\PayPal;
            $response = $paypal->purchase([
                'amount'        => $paypal->formatAmount($payment->amount),
                'transactionId' => $payment->id,
                'currency'      => env('CURRENCY_CODE'),
                'cancelUrl'     => $paypal->getCancelUrl($hash),
                'returnUrl'     => $paypal->getReturnUrl($hash),
            ]);

            if ($response->isRedirect()) {
                $response->redirect();
            }

            return redirect('/view/payment/' . $payment->hash)
                ->with(['message' => $response->getMessage()]);
        }
        abort(404);
    }
}
