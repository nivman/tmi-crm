<?php

namespace App;

use App\Traits\VueTable;
use App\Traits\LogActivity;
use App\Traits\AccountingJournal;
use App\Traits\AttributableModel;
use Illuminate\Database\Eloquent\Model;

class EmailSettings extends Model
{
    use LogActivity, VueTable;

    public static $columns = ['id', 'attribute', 'value'];

    protected $fillable = ['attribute', 'value'];

}
