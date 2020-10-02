<?php

namespace App;

use App\Traits\VueTable;
use App\Traits\LogActivity;
use Illuminate\Database\Eloquent\Model;

class Transfer extends Model
{
    use LogActivity, VueTable;

    public static $columns = ['id', 'fromAccount.name', 'toAccount.name', 'amount', 'details', 'user_id'];

    protected $guarded = [];
    protected $with    = ['fromAccount', 'toAccount'];

    public function fromAccount()
    {
        return $this->belongsTo(Account::class, 'from');
    }

    public function toAccount()
    {
        return $this->belongsTo(Account::class, 'to');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
