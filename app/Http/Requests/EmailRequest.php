<?php

namespace App\Http\Requests;

use App\EmailSettings;
use App\Helpers\Date;
use App\Task;
use Illuminate\Support\Arr;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Crypt;
use function Aws\filter;


class EmailRequest extends FormRequest
{


    public function rules()
    {

        return [

            'main_mail_address' => 'email:rfc,dns',
            'mail_from_name' => 'required',
            'imap_user_name' => 'required',
            'mail_host' => 'required|string',
            'mail_port' => 'nullable',
            'mail_user_name' => 'nullable',
            'mail_password' => 'required',
            'mail_encryption' => 'nullable',
            'mail_driver' => 'nullable',
            'lead_title' => 'nullable',
            'event_title' => 'nullable',
            'task_title' => 'nullable',
            'imap_port' => 'required'
        ];

    }

    protected function prepareForValidation()
    {
        $emailSettings = json_decode($this->getContent(),true);
        $mail_password = $this['mail_password'];
        if(!$mail_password) {
            $mail_password = EmailSettings::find($emailSettings['id'])->toArray()['mail_password'];
            $this->merge(['mail_password' =>$mail_password]);
        }else{
            $this->merge(['mail_password' =>Crypt::encrypt($mail_password)]);
        }
        $this->merge(['task_title' => array_column($emailSettings['tasks'], 'task')]);
        $this->merge(['event_title' => array_column($emailSettings['events'], 'event')]);
        $this->merge(['lead_title' => array_column($emailSettings['leads'], 'lead')]);
    }

    public function validated()
    {
        $rules = $this->container->call([$this, 'rules']);

        return $this->only(collect($rules)->keys()->map(function ($rule) {
            return str_contains($rule, '.') ? explode('.', $rule)[0] : $rule;
        })->unique()->toArray());

    }
}
