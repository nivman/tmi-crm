<?php

namespace App\Http\Controllers;

use Cache;
use App\CustomField;
use Illuminate\Http\Request;

class CustomFieldsController extends Controller
{
    protected function isValid($request)
    {
        $v = $request->validate([
            'name'        => 'required',
            'slug'        => 'required|alpha_dash',
            'type'        => 'required',
            'description' => 'nullable',
            'is_required' => 'nullable',
            'sort_order'  => 'nullable|numeric',
            'entities'    => 'required|array',
            'hide_in_list' => 'nullable'
        ]);
        if (in_array('App\Invoice', $v['entities'])) {
            array_push($v['entities'], 'App\Recurring');
        }
        return $v;
    }

    public function destroy(CustomField $customField)
    {
        $customField->delete();
        Cache::flush();
        return response(['success' => true], 204);
    }

    public function index()
    {
        return response()->json(CustomField::vueTable(CustomField::$columns));
    }

    public function show(CustomField $customField)
    {
        return $customField;
    }

    public function store(Request $request)
    {
        $v  = $this->isValid($request);
        $cf = CustomField::create($v);
        Cache::flush();
        return $cf;
    }

    public function update(Request $request, CustomField $customField)
    {

        $v = $this->isValid($request);

        $customField->fill($v)->save();
        Cache::flush();
        return $customField->refresh();
    }
}
