<?php

namespace App;

use App\Traits\VueTable;
use App\Traits\LogActivity;
use App\Traits\AccountingJournal;
use App\Traits\AttributableModel;
use Illuminate\Database\Eloquent\Model;

class EmailSettings extends Model
{

    public $timestamps = false;
    public static $columns = [
        'main_mail_address',
        'mail_from_name',
        'imap_user_name',
        'mail_host',
        'mail_port',
        'mail_user_name',
        'mail_password',
        'mail_encryption',
        'mail_driver',
        'imap_port'
    ];

    protected $fillable = [
        'main_mail_address',
        'mail_from_name',
        'imap_user_name',
        'mail_host',
        'mail_port',
        'mail_user_name',
        'mail_password',
        'mail_encryption',
        'mail_driver',
        'imap_port',
        'lead_title',
        'event_title',
        'task_title',
        'imap_port'
        ];

}
