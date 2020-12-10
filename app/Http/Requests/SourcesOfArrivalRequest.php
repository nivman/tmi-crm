<?php

namespace App\Http\Requests;

use App\Product;
use App\Status;
use Illuminate\Support\Arr;
use Illuminate\Foundation\Http\FormRequest;

class SourcesOfArrivalRequest extends FormRequest
{
    public $attributes;

    public function authorize()
    {
        return true;
        // return $this->user()->hasRole('admin');
    }

    public function rules()
    {
        return [

            'name' => 'required|max:55',
            'color' => 'max:55',
        ];

    }

    public function validated()
    {
        $rules = $this->container->call([$this, 'rules']);

        return $this->only(collect($rules)->keys()->map(function ($rule) {
            return str_contains($rule, '.') ? explode('.', $rule)[0] : $rule;
        })->unique()->toArray());
    }
}
