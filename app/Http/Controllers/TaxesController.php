<?php

namespace App\Http\Controllers;

use App\Tax;
use Illuminate\Http\Request;

class TaxesController extends Controller
{
    protected function isValid($request, $id = null)
    {
        return $request->validate([
            'code'        => 'required|alpha_num|max:20|unique:taxes,code,' . $id,
            'rate'        => 'nullable|numeric',
            'details'     => 'nullable',
            'name'        => 'required',
            'state'       => 'boolean',
            'same'        => 'boolean',
            'number'      => 'nullable',
            'compound'    => 'boolean',
            'recoverable' => 'boolean',
        ]);
    }

    public function destroy(Tax $tax)
    {
        if ($tax->products()->exists()) {
            return response(['message' => 'Tax has been attached to some products and can not be deleted.'], 422);
        } elseif ($tax->invoices()->exists()) {
            return response(['message' => 'Tax has been attached to some invoices and can not be deleted.'], 422);
        } elseif ($tax->purchases()->exists()) {
            return response(['message' => 'Tax has been attached to some purchases and can not be deleted.'], 422);
        }
        $tax->delete();
        return response(['success' => true], 204);
    }

    public function index(Request $request)
    {
        if ($request->all) {
            return Tax::select(['*', 'id as value'])->orderBy('name', 'asc')->get();
        }
        if (!auth()->user()->hasRole('admin')) {
            abort(403, 'Access denied!');
        }
        return response()->json(Tax::vueTable(Tax::$columns));
    }

    public function show(Tax $tax)
    {
        return $tax;
    }

    public function store(Request $request)
    {
        $v = $this->isValid($request);
        return Tax::create($v);
    }

    public function update(Request $request, Tax $tax)
    {
        $v = $this->isValid($request, $tax->id);
        $tax->update($v);
        return $tax;
    }
}
