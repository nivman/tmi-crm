<?php

namespace App\Http\Requests;

use App\Purchase;
use Illuminate\Support\Arr;
use Illuminate\Foundation\Http\FormRequest;

class PurchaseRequest extends FormRequest
{
    public $attributes;

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $rules = [
            'date'      => 'required|date_format:Y-m-d',
            'reference' => 'nullable|max:50',
            'vendor_id' => 'required',
            'draft'     => 'nullable',
            'taxes'     => 'nullable|array',
            'discount'  => 'nullable',
            'shipping'  => 'nullable',
            'note'      => 'nullable',
            'products'  => function ($attribute, $value, $fail) {
                if (empty($value)) {
                    return $fail('Please add at least one product.');
                }
            },
            'products.*.id'       => 'nullable',
            'products.*.name'     => 'required|max:255',
            'products.*.cost'     => 'required|numeric',
            'products.*.qty'      => 'required|numeric',
            'products.*.allTaxes' => 'nullable|array',
            'products.*.taxes'    => 'nullable|array',
        ];

        $purchase         = new Purchase;
        $this->attributes = $purchase->attributes();
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

        return $validated;
    }
}
