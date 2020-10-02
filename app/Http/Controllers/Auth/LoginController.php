<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    protected $redirectTo = RouteServiceProvider::HOME;

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    protected function authenticated(Request $request, $user)
    {
        log_activity('User has been logged in', ['user' => $user], $user);
        if (request()->ajax()) {
            return response([
                'name'        => $user->name,
                'email'       => $user->email,
                'username'    => $user->username,
                'roles'       => $user->roles->pluck('slug'),
                'customer_id' => $user->customer_id,
                'vendor_id'   => $user->vendor_id,
            ], 200);
            exit;
        }
    }

    protected function credentials(Request $request)
    {
        $field = filter_var($request->username, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        return [
            $field     => $request->username,
            'password' => $request->get('password'),
        ];
    }

    public function decayMinutes()
    {
        return property_exists($this, 'decayMinutes') ? $this->decayMinutes : 10;
    }

    public function logout(Request $request)
    {
        if ($user = $request->user()) {
            log_activity('User is logging out', ['user' => $user], $user);
            $user->disableLogging();
            $this->guard()->logout();
            $request->session()->invalidate();
        }
        if (request()->ajax()) {
            return response(['success' => true], 200);
        }
        return redirect('/');
    }

    public function maxAttempts()
    {
        return property_exists($this, 'maxAttempts') ? $this->maxAttempts : 3;
    }

    public function username()
    {
        return 'username';
    }
}
