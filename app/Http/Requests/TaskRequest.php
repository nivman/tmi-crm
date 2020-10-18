<?php

namespace App\Http\Requests;

use App\Helpers\Date;
use App\Task;
use App\TaskPriority;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class TaskRequest extends FormRequest
{
    public $attributes;


    public function rules()
    {

         $rules = [

            'name' => 'required|max:255',
            'details' => 'nullable',
            'start_date' => 'nullable|date_format:Y-m-d H:i',
            'end_date'   => 'nullable|date_format:Y-m-d H:i|after_or_equal:start_date',
            'customer_id' => 'nullable',
            'priority_id'    => 'nullable',
            'event_id'   => 'nullable',
            'status_id' => 'nullable'
        ];

        $task = new Task;
        $this->attributes = $task->attributes();
        foreach ($this->attributes as $key => $attribute) {
            $rules = Arr::add($rules, $key, ($attribute->is_required ? 'required' : 'nullable'));
        }

        return $rules;
    }

    protected function prepareForValidation()
    {
        $format_date = Date::formatDate($this);

        $this->merge(['start_date'=> $format_date['start_date'], 'end_date' => $format_date['end_date']]);
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
