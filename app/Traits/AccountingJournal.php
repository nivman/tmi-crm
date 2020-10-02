<?php

namespace App\Traits;

use App\Accounting\Journal;

trait AccountingJournal
{
    public function journal()
    {
        return $this->morphOne(Journal::class, 'morphed');
    }

    public function initJournal($currency_code = 'USD')
    {
        if (!$this->journal) {
            $journal = new Journal();
            $journal->currency = $currency_code;
            $journal->balance = 0;
            return $this->journal()->save($journal);
        }
        throw new \Exception('Journal already exists.');
    }
}
