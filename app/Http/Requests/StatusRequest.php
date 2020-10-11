<?php

namespace App\Http\Requests;

use App\Product;
use Illuminate\Support\Arr;
use Illuminate\Foundation\Http\FormRequest;

class StatusRequest extends FormRequest
{
    public $attributes;

    public function authorize()
    {
        return true;
        // return $this->user()->hasRole('admin');
    }

    public function rules()
    {
        $rules = [

            'name' => 'required|max:55',
            'entity_name' => 'required|max:55',
            'color' => 'max:55',
        ];

        $product = new Product;
        $this->attributes = $product->attributes();
        foreach ($this->attributes as $key => $attribute) {
            $rules = Arr::add($rules, $key, ($attribute->is_required ? 'required' : 'nullable'));
        }

        return $rules;
    }

    public function validated()
    {
        $rules = $this->container->call([$this, 'rules']);

        return $this->only(collect($rules)->keys()->map(function ($rule) {
            return str_contains($rule, '.') ? explode('.', $rule)[0] : $rule;
        })->unique()->toArray());
    }
}
