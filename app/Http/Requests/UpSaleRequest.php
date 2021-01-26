<?php

namespace App\Http\Requests;

use App\Expense;
use Illuminate\Support\Arr;
use Illuminate\Foundation\Http\FormRequest;

class UpSaleRequest extends FormRequest
{
    public $attributes;

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $rules = [

            'title' => 'required|max:255',
            'amount' => 'required|numeric',
            'category_id' => 'required',
            'project_id' => 'required'
        ];

        $expense = new Expense;
        $this->attributes = $expense->attributes();
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
