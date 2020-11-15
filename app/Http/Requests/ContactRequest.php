<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;



class ContactRequest extends FormRequest
{

    public function rules()
    {

        return [
            'first_name' => 'required|max:55',
            'last_name' => 'nullable',
            'email' => 'nullable',
            'phone' => 'nullable',
            'customer_id' => 'required',
        ];

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
