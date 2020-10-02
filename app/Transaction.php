<?php

namespace App;

use App\Traits\VueTable;
use App\Accounting\JournalTransaction;

class Transaction extends JournalTransaction
{
    use VueTable;

    public static $columns = ['id', 'created_at', 'debit', 'credit', 'type', 'subject_type', 'subject_id'];
    protected $hidden      = ['posted_at', 'updated_at', 'deleted_at'];
    protected $table       = 'journal_transactions';
}
