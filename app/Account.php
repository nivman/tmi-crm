<?php

namespace App;

use App\Traits\VueTable;
use App\Traits\LogActivity;
use App\Traits\AccountingJournal;
use App\Traits\AttributableModel;
use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    use AccountingJournal, AttributableModel, LogActivity, VueTable;

    public static $columns = ['id', 'name', 'type', 'reference', 'details', 'offline'];

    protected $fillable = ['name', 'type', 'reference', 'details', 'opening_balance', 'offline'];
    protected $hidden   = ['created_at', 'updated_at'];

    protected static function boot()
    {
        parent::boot();

        static::created(function ($account) {
            $account->initJournal();
            if ($account->opening_balance) {
                $account->refresh()->journal->creditDollars($account->opening_balance, 'opening_balance');
            }
        });
    }

    public static function scopeOffline($query)
    {
        return $query->where('offline', 1);
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }

    public function fromTransfers()
    {
        return $this->hasMany(Transfer::class, 'from');
    }

    public function incomes()
    {
        return $this->hasMany(Income::class);
    }

    public function toTransfers()
    {
        return $this->hasMany(Transfer::class, 'to');
    }
}
