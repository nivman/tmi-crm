<?php

namespace App\Http\Requests;

use App\Product;
use Illuminate\Support\Arr;
use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
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
            'details' => 'nullable',
            'name' => 'required|max:55',
            'qty' => 'nullable|numeric',
            'cost' => 'required|numeric',
            'price' => 'required|numeric',
            'code' => 'required|alpha_dash|max:25|unique:products,code,' . $this->id,
            'category' => 'required|integer',
            'taxes' => 'nullable|array',
            'image' => 'sometimes|nullable|mimes:jpg,jpeg,gif,png|dimensions:min_width=80,max_width=800,min_height=80,max_height=800',
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
