<?php

namespace App\Http\Requests;

use App\Expense;
use Illuminate\Support\Arr;
use Illuminate\Foundation\Http\FormRequest;

class ExpenseRequest extends FormRequest
{
    public $attributes;

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $rules = [
            'details' => 'nullable',
            'title' => 'required|max:55',
            'amount' => 'required|numeric',
            'reference' => 'nullable|max:255',
            'account_id' => 'required|numeric',
            'category' => 'required|integer',
            'taxes' => 'nullable|array',
            'project_id' => 'nullable'
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
