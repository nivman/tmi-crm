<?php

namespace App\Http\Controllers;

use Geographer;
use Illuminate\Http\Request;

class AjaxController extends Controller
{
    public function countries()
    {
        return collect(Geographer::getCountries()->useShortNames()->sortBy('name')->toArray())
        ->transform(function ($item, $key) {
            return ['value' => $item['isoCode'], 'label' => $item['name']];
        });
    }

    public function data()
    {
        $user = auth()->check() ? auth()->user() : false;
        return [
            'user' => $user ? [
                'name'        => $user->name,
                'email'       => $user->email,
                'phone'       => $user->phone,
                'username'    => $user->username,
                'roles'       => $user->roles->pluck('slug'),
                'customer_id' => $user->customer_id,
                'vendor_id'   => $user->vendor_id,
            ] : null,
            'settings' => [
                'demo'    => demo(),
                'stock'   => stock(),
                'ac'      => app_config(),
                'baseURL' => url('/'),
                'app'     => ['name' => config('app.name')],
                'system'  => ['card_gateway' => env('CARD_GATEWAY'), 'default_account_id' => env('DEFAULT_ACCOUNT')],
            ],
            'notifications' => [
                'payment_due'    => \App\Payment::where('received', '!=', 1)->count(),
                'payment_review' => \App\Payment::where('gateway', 'offline')->where('review', 1)->count(),
            ],
        ];
    }

    public function file($file)
    {
        return response()->file(storage_path('app/payments/') . $file);
    }

    public function home()
    {
        return view('home');
    }

    public function jsRout(Request $request)
    {
        return redirect()->to('/' . $request->path());
    }

    public function manifest()
    {
        return [
            'short_name'       => app_config('short_name'),
            'name'             => config('app.name'),
            'background_color' => '#3273dc',
            'orientation'      => 'portrait-primary',
            'theme_color'      => '#3273dc',
            'start_url'        => url('/') . '?app=true',
            'display'          => 'standalone',
            'icons'            => [
                ['src' => 'icon-48.png', 'type' => 'image/png', 'sizes' => '48x48'],
                ['src' => 'icon-76.png', 'type' => 'image/png', 'sizes' => '76x76'],
                ['src' => 'icon-96.png', 'type' => 'image/png', 'sizes' => '96x96'],
                ['src' => 'icon-120.png', 'type' => 'image/png', 'sizes' => '120x120'],
                ['src' => 'icon-144.png', 'type' => 'image/png', 'sizes' => '144x144'],
                ['src' => 'icon-152.png', 'type' => 'image/png', 'sizes' => '152x152'],
                ['src' => 'icon-192.png', 'type' => 'image/png', 'sizes' => '192x192'],
                ['src' => 'icon-512.png', 'type' => 'image/png', 'sizes' => '512x512'],
            ],
        ];
    }

    public function states(Request $request)
    {
        return collect(Geographer::findOneByCode($request->country)->getStates()->sortBy('name')->toArray())
        ->transform(function ($item, $key) {
            return ['value' => $item['isoCode'], 'label' => $item['name']];
        });
    }

    public function token(Request $request)
    {
        return ['token' => csrf_token()];
    }
}
