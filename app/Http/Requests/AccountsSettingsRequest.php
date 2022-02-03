<?php

namespace App\Http\Requests;

use App\AccountSettings;
use App\AccountsSettings;
use App\Helpers\Date;

use App\Project;
use Illuminate\Support\Arr;
use Illuminate\Foundation\Http\FormRequest;



class AccountsSettingsRequest extends FormRequest
{
    public $attributes;


    public function rules()
    {

        $rules = [
            'price' => 'required|integer',
        ];

        $accountSettings = new AccountsSettings();
        $this->attributes = $accountSettings->attributes();

        foreach ($this->attributes as $key => $attribute) {
            $rules = Arr::add($rules, $key, ($attribute->is_required ? 'required' : 'nullable'));
        }

        return $rules;
    }

    public function validated()
    {

        $rules = $this->container->call([$this, 'rules']);

        $validated = $this->only(collect($rules)->keys()->map(function ($rule) {
            return str_contains($rule, '.') ? explode('.', $rule)[0] : $rule;
        })->unique()->toArray());


        return $validated;
    }
}
