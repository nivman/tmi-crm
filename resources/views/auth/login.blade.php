@extends('layouts.app')

@section('content')
<div class="login-container">
    <div class="login">
        <div class="login-box">
            <div class="box-part is-radiusless is-radius-top-left is-radius-bottom-left">
                <div id="partition-register" class="partition p-b-md">
                    <div class="partition-title">Please sign in</div>
                    <div class="partition-form">
                        @if (session('status'))
                        <p class="has-text-success m-b-sm">{{ session('status') }}</p>
                        @endif
                        @if ($errors->has('token'))
                        <p class="has-text-danger m-b-sm">
                            {{ $errors->first('token') }}
                        </p>
                        @endif
                        @if ($errors->has('username'))
                        <p class="has-text-danger m-b-sm">
                            {{ $errors->first('username') }}
                        </p>
                        @endif
                        @if ($errors->has('password'))
                        <p class="has-text-danger m-b-sm">
                            {{ $errors->first('password') }}
                        </p>
                        @endif
                        <p>Please login to access your account</p>
                        <form class="m-t-md m-b-md" method="POST" action="{{ route('login') }}" autocomplete="false">
                            {{ csrf_field() }}
                            <div class="field">
                                <input type="text" placeholder="Username" name="username" value="{{ old('username') }}" required autofocus class="input">
                            </div>
                            <div class="field">
                                <input type="password" placeholder="Password" name="password" required class="input">
                            </div>
                            <div class="field">
                                <label for="remember" class="label-checkbox">
                                    <input type="checkbox" class="invisible" id="remember" name="remember" {{ old('remember') ? 'checked' : '' }}>
                                    <div class="checkbox">
                                        <svg width="20px" height="20px" viewBox="0 0 20 20">
                                            <path d="M3,1 L17,1 L17,1 C18.1045695,1 19,1.8954305 19,3 L19,17 L19,17 C19,18.1045695 18.1045695,19 17,19 L3,19 L3,19 C1.8954305,19 1,18.1045695 1,17 L1,3 L1,3 C1,1.8954305 1.8954305,1 3,1 Z"></path>
                                            <polyline points="4 11 8 15 16 6"></polyline>
                                        </svg>
                                    </div>
                                    <span>Remember Me</span>
                                </label>
                            </div>
                            <button type="submit" class="button is-fullwidth is-outlined">Sign In</button>
                        </form>
                        <a class="m-t-md p-l-none" href="{{ route('password.request') }}">
                            Forgot Your Password?
                        </a>
                    </div>
                </div>
            </div>
            <div class="box-part right is-radiusless is-radius-top-right is-radius-bottom-right">
                <div class="bg is-radius-top-right is-radius-bottom-right"></div>
            </div>
        </div>
    </div>
</div>
<footer>
    <div class="container">
        <span class="text-muted">&copy; {{ date('Y') }}  {{ config('app.name') }}</span>
    </div>
</footer>
@endsection
