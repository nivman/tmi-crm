<?php

namespace App\Http\Controllers;

use App\Transfer;
use Illuminate\Http\Request;

class TransfersController extends Controller
{
    protected function isValid($request, $id = null)
    {
        return $request->validate([
            'details' => 'nullable',
            'to'      => 'required|numeric',
            'from'    => 'required|numeric',
            'amount'  => 'required|numeric',
        ]);
    }

    public function destroy(Transfer $transfer)
    {
        $transfer->delete();
        return response(['success' => true], 204);
    }

    public function index()
    {
        return response()->json(Transfer::vueTable(Transfer::$columns));
    }

    public function show(Transfer $transfer)
    {
        return $transfer;
    }

    public function store(Request $request)
    {
        $v            = $this->isValid($request);
        $v['user_id'] = auth()->id();
        return Transfer::create($v);
    }

    public function update(Request $request, Transfer $transfer)
    {
        $v = $this->isValid($request, $transfer->id);
        $transfer->update($v);
        return $transfer;
    }
}
