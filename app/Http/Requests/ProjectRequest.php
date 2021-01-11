<?php

namespace App\Http\Requests;

use App\Helpers\Date;

use App\Project;
use Illuminate\Support\Arr;
use Illuminate\Foundation\Http\FormRequest;



class ProjectRequest extends FormRequest
{
    public $attributes;


    public function rules()
    {

        $rules = [
            'name' => 'max:255',
            'start_date' => 'nullable|date_format:Y-m-d',
            'end_date' => 'nullable|date_format:Y-m-d|after_or_equal:start_date',
            'customer_id' => 'nullable',
            'type_id' =>  'nullable',
            'type' =>   'nullable',
            'price' => 'nullable',
            'expenses' => 'nullable',
            'active' => 'required',
        ];

        $project = new Project();
        $this->attributes = $project->attributes();
        foreach ($this->attributes as $key => $attribute) {
            $rules = Arr::add($rules, $key, ($attribute->is_required ? 'required' : 'nullable'));
        }

        return $rules;
    }

    protected function prepareForValidation()
    {

        if ($this['start_date'] && $this['end_date']) {

            $format_date = Date::formatDate($this);

            $this->merge(['start_date' => $format_date['start_date'], 'end_date' => $format_date['end_date']]);
        }
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
