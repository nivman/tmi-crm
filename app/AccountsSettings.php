<?php

namespace App;

use App\Http\ModelForm;
use App\Traits\AttributableModel;
use App\Traits\LogActivity;
use App\Traits\Restrictable;
use Illuminate\Support\Facades\DB;

class AccountsSettings extends ModelForm
{
    use  AttributableModel, LogActivity, Restrictable;

    public static $columns = ['id', 'created_at', 'updated_at', 'price'];
    protected $fillable = ['price'];
    protected $hidden = ['created_at', 'updated_at'];


    public function getAccountsSettings()
    {
        return DB::table('accounts_settings')->select('id', 'price')->get()->toArray()[0] ;


    }

}