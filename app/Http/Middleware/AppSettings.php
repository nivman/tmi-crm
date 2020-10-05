<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Storage;


class AppSettings
{
    public function handle($request, Closure $next)
    {
      $setting = $request->session()->get('appSettings');
        $input = $request->except(['_token', '_method']);

      //  $settings =   Storage::disk('local')->put('settings.json', json_encode($input, JSON_PRETTY_PRINT));
      //  if (!$settings) {

            $settings = json_decode(Storage::disk('local')->get('settings.json'), true);

            $request->session()->put('appSettings', $settings);
     //   }

        // foreach ($settings as $setting => $rows) {
        //     foreach ($rows as $key => $value) {
        //         config(["{$setting}.{$key}" => $value]);
        //     }
        // }

        return $next($request);
    }
}
