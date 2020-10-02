<?php

namespace App;

use App\Traits\LogActivity;
use App\Traits\Restrictable;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use LogActivity, Restrictable;

    protected $fillable = ['title', 'details', 'start_date', 'end_date', 'color', 'user_id'];
    protected $hidden   = ['created_at', 'updated_at'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
