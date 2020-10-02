<?php

namespace App\Http\Controllers\Update;

use App\Http\Controllers\Controller;

class UpdateController extends Controller
{
    public function __invoke()
    {
        $code = \Artisan::call('migrate', ['--force' => true, '--path' => 'database/migrations/updates']);
        return redirect('/message')->with('info', trim(\Artisan::output()));
    }
}
