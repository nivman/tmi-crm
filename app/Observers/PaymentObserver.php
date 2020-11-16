<?php

namespace App\Observers;

use App\Account;
use App\Payment;

class PaymentObserver
{
    public function created(Payment $payment)
    {
        if ($payment->received) {
            $payer   = $payment->payable;
            $account = Account::find($payment->account_id);

            $payer_record = $payer->journal
                ->debitDollars($payment->amount, 'payment_added')
                ->referencesObject($payment);

            if ($payer instanceof \App\Customer) {
                $account_record = $account->journal
                    ->creditDollars($payment->amount, 'payment_received')
                    ->referencesObject($payment);
            } else {
                $account_record = $account->journal
                    ->debitDollars($payment->amount, 'payment_sent')
                    ->referencesObject($payment);
            }

            $payment->disableLogging();
            $payment->update([
                'payer_transaction_id'   => $payer_record->id,
                'account_transaction_id' => $account_record->id,
            ]);
            event(new \App\Events\PaymentEvent($payment));
            if ($payment->payable->email) {
                try {
                    \Mail::to($payment->payable->email)->send(new \App\Mail\PaymentReceived($payment));
                } catch (\Exception $e) {
                    \Log::error('Unable to send email, please check your system settings. Error: ' . $e->getMessage());
                }
            }
        } else {
            if ($payment->payable->email) {
                try {
                    \Mail::to($payment->payable->email)->send(new \App\Mail\PaymentCreated($payment));
                } catch (\Exception $e) {
                    \Log::error('Unable to send email, please check your system settings. Error: ' . $e->getMessage());
                }
            }
        }
    }

    public function deleting(Payment $payment)
    {
        if ($payment->received) {
            $payer   = $payment->payable;
            $account = Account::find($payment->account_id);

            $payer_record = $payer->journal
                ->creditDollars($payment->amount, 'payment_deleted')
                ->referencesObject($payment);

            if ($payer instanceof \App\Customer) {
                $account_record = $account->journal
                    ->debitDollars($payment->amount, 'payment_deleted')
                    ->referencesObject($payment);
            } else {
                $account_record = $account->journal
                    ->creditDollars($payment->amount, 'payment_deleted')
                    ->referencesObject($payment);
            }
            event(new \App\Events\PaymentEvent($payment, false, true));
        }
    }

    public function updating(Payment $payment)
    {
        if (!$payment->wasRecentlyCreated) {
            if ($payment->received) {
                if (!$payment->getOriginal('received')) {
                    $payer   = $payment->payable;
                    $account = Account::find($payment->account_id);

                    $payer_record = $payer->journal
                        ->debitDollars($payment->amount, 'payment_added')
                        ->referencesObject($payment);

                    if ($payer instanceof \App\Customer) {
                        $account_record = $account->journal
                            ->creditDollars($payment->amount, 'payment_received')
                            ->referencesObject($payment);
                    } else {
                        $account_record = $account->journal
                            ->debitDollars($payment->amount, 'payment_sent')
                            ->referencesObject($payment);
                    }

                    $payment->disableLogging();
                    $temp = $payment->getEventDispatcher();
                    $payment->unsetEventDispatcher();
                    $payment->update([
                        'payer_transaction_id'   => $payer_record->id,
                        'account_transaction_id' => $account_record->id,
                    ]);
                    $payment->setEventDispatcher($temp);
                    event(new \App\Events\PaymentEvent($payment));
                    if ($payment->payable->email) {
                        try {
                            \Mail::to($payment->payable->email)->send(new \App\Mail\PaymentReceived($payment));
                        } catch (\Exception $e) {
                            \Log::error('Unable to send email, please check your system settings. Error: ' . $e->getMessage());
                        }
                    }
                } elseif ($payment->amount != $payment->getOriginal('amount')) {
                    $payer = $payment->getOriginal('payable_type')::find($payment->getOriginal('payable_id'));
                    log_activity('Updating payment', $payment, $payer);
                    $account = Account::find($payment->getOriginal('account_id'));

                    $payer_record = $payer->journal
                        ->creditDollars($payment->getOriginal('amount'), 'payment_updating')
                        ->referencesObject($payment);

                    if ($payer instanceof \App\Customer) {
                        $account_record = $account->journal
                            ->debitDollars($payment->getOriginal('amount'), 'payment_updating')
                            ->referencesObject($payment);
                    } else {
                        $account_record = $account->journal
                            ->creditDollars($payment->getOriginal('amount'), 'payment_updating')
                            ->referencesObject($payment);
                    }

                    $payer   = $payment->payable;
                    $account = Account::find($payment->account_id);

                    $payer_record = $payer->journal
                        ->debitDollars($payment->amount, 'payment_updated')
                        ->referencesObject($payment);

                    if ($payer instanceof \App\Customer) {
                        $account_record = $account->journal
                            ->creditDollars($payment->amount, 'payment_updated')
                            ->referencesObject($payment);
                    } else {
                        $account_record = $account->journal
                            ->debitDollars($payment->amount, 'payment_updated')
                            ->referencesObject($payment);
                    }
                    event(new \Modules\MPS\Events\PaymentEvent($payment, true));
                }
            }
        }
    }
}
