<?php

namespace App\Http\Requests;

use App\Product;
use App\Status;
use Illuminate\Support\Arr;
use Illuminate\Foundation\Http\FormRequest;

class NotesRequest extends FormRequest
{
    public $attributes;

    public function rules()
    {
        return [

            'title' => 'required|max:525',
            'subject' => 'nullable',
            'note_category_id' => 'nullable',
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
