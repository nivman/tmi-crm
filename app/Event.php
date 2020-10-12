<?php

namespace App;

use App\Traits\LogActivity;
use App\Traits\Restrictable;
use App\Traits\VueTable;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use LogActivity, Restrictable, VueTable;

    public static $columns = ['title', 'details', 'start_date', 'end_date', 'color', 'user_id'];
    protected $fillable = ['title', 'details', 'start_date', 'end_date', 'color', 'user_id', 'contact_id'];
    protected $hidden   = ['created_at', 'updated_at'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function contact()
    {
        return $this->belongsTo(Contact::class);
    }
}
