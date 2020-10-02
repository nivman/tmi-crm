<?php

namespace App\Observers;

use App\Transfer;

class TransferObserver
{
    public function created(Transfer $transfer)
    {
        $to_transfer_record = $transfer->toAccount->journal
            ->creditDollars($transfer->amount, 'transfer_created')
            ->referencesObject($transfer);
        $from_transfer_record = $transfer->fromAccount->journal
            ->debitDollars($transfer->amount, 'transfer_created')
            ->referencesObject($transfer);
        $transfer->disableLogging();
        $transfer->update([
            'to_transaction_id'   => $to_transfer_record->id,
            'from_transaction_id' => $from_transfer_record->id,
        ]);
    }

    public function deleting(Transfer $transfer)
    {
        $to_transfer_record = $transfer->toAccount->journal
            ->debitDollars($transfer->amount, 'transfer_deleted')
            ->referencesObject($transfer);
        $from_transfer_record = $transfer->fromAccount->journal
            ->creditDollars($transfer->amount, 'transfer_deleted')
            ->referencesObject($transfer);
    }

    public function updating(Transfer $transfer)
    {
        if (!$transfer->wasRecentlyCreated && $transfer->getOriginal('amount') != $transfer->amount) {
            $to_transfer_record = $transfer->toAccount
                ->journal->debitDollars($transfer->getOriginal('amount'), 'transfer_updating')
                ->referencesObject($transfer);
            $from_transfer_record = $transfer->fromAccount
                ->journal->creditDollars($transfer->getOriginal('amount'), 'transfer_updating')
                ->referencesObject($transfer);

            $to_transfer_record = $transfer->toAccount->journal
                ->creditDollars($transfer->amount, 'transfer_updated')
                ->referencesObject($transfer);
            $from_transfer_record = $transfer->fromAccount->journal
                ->debitDollars($transfer->amount, 'transfer_updated')
                ->referencesObject($transfer);
        }
    }
}
