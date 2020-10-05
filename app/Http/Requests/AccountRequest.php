<?php

namespace App\Http\Requests;

use App\Account;
use Illuminate\Support\Arr;
use Illuminate\Foundation\Http\FormRequest;

class AccountRequest extends FormRequest
{
    public $attributes;

    public function authorize()
    {
        return $this->user()->hasRole('admin');
    }

    public function rules()
    {
        $rules = [
            'details'         => 'nullable',
            'name'            => 'required|max:55|unique:accounts,name,' . $this->id,
            'type'            => 'required|max:25',
            'reference'       => 'nullable|max:255',
            'opening_balance' => 'nullable|numeric',
            'offline'         => 'nullable|boolean'
        ];

        $account          = new Account;
        $this->attributes = $account->attributes();
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
;
        if (!isset($validated['opening_balance']) && empty($validated['opening_balance'])) {
            $validated['opening_balance'] = 0;
        }

        $validated['offline'] = $validated['offline'] == true ? 1 : 0;

        foreach ($this->attributes as $attribute) {
            if ($attribute->type == 'datetime' && !empty($validated[$attribute->slug])) {
                $validated[$attribute->slug] = \Carbon\Carbon::parse($validated[$attribute->slug]);
            }
        }

        return $validated;
    }
}
