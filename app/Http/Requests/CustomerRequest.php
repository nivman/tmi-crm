<?php

namespace App\Http\Requests;

use App\Customer;
use Illuminate\Support\Arr;
use Illuminate\Foundation\Http\FormRequest;

class CustomerRequest extends FormRequest
{
    public $attributes;

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $rules = [
            'phone' => 'nullable',
            'state' => 'nullable',
            'address' => 'nullable',
            'company' => 'nullable',
            'country' => 'nullable',
            'name' => 'required|max:55',
            'opening_balance' => 'nullable|numeric',
            'email' => 'nullable|email|unique:customers,email,' . $this->id,
        ];

        $customer = new Customer;
        $this->attributes = $customer->attributes();
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

        foreach ($this->attributes as $attribute) {
            if ($attribute->type == 'datetime' && !empty($validated[$attribute->slug])) {
                $validated[$attribute->slug] = \Carbon\Carbon::parse($validated[$attribute->slug]);
            }
        }
//
//        $cs = getCS($validated['country'], $validated['state']);
//        $validated['state_name'] = $cs['state']->name;
//        $validated['country_name'] = $cs['country']->name;

        return $validated;
    }
}
