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
        //dd(Storage::disk('local')->get('settings.json'));
        Storage::disk('local')->put('settings.json', json_encode($input, JSON_PRETTY_PRINT));
        $request->session()->forget('appSettings');
        //dd($input);
        return response($input, 201);
    }

    public function update(Request $request)
    {
        if (demo()) {
            return response(['message' => 'This feature is disabled on demo.'], 422);
        }

        $data = $request->except(['_token', '_method']);
        if (Env::update($data)) {
            return response($data, 201);
        }
        return response(['message' => 'Unable to update settings.'], 422);
    }
}
