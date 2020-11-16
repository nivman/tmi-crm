<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use App\Helpers\Env;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function __construct()
    {

        $this->middleware(['auth', 'role:super']);
    }

    public function index(Request $request)
    {
        return $request->session()->get('appSettings');
    }

    public function show(Request $request)
    {

        return Env::changeable();
    }

    public function store(Request $request)
    {
        $input = $request->except(['_token', '_method']);

        Storage::disk('local')->put('settings.json', json_encode($input, JSON_PRETTY_PRINT));
        $request->session()->forget('appSettings');

        return response($input, 201);
    }

    public function update(Request $request)
    {

        $data = $request->except(['_token', '_method']);

        if (Env::update($data)) {
            Storage::disk('local')->put('settings.json', json_encode($data, JSON_PRETTY_PRINT));
            $request->session()->forget('appSettings');
            return response($data, 201);
        }
        return response(['message' => 'Unable to update settings.'], 422);
    }
}
