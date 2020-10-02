@extends('layouts.app')

@section('content')
<div class="login-container">
    <div class="login">
        <div class="login-box">
            <div class="box-part rounded-left">
                <div id="partition-register" class="partition p-b-md">
                    <div class="partition-title">Reset Password</div>
                    <div class="partition-form">
                        @if (session('status'))
                        <p class="has-text-success m-b-sm">{{ session('status') }}</p>
                        @endif
                        @if ($errors->has('token'))
                        <p class="has-text-danger m-b-sm">
                            {{ $errors->first('token') }}
                        </p>
                        @endif
                        @if ($errors->has('email'))
                        <p class="has-text-danger m-b-sm">
                            {{ $errors->first('email') }}
                        </p>
                        @endif
                        @if ($errors->has('password'))
                        <p class="has-text-danger m-b-sm">
                            {{ $errors->first('password') }}
                        </p>
                        @endif
                        @if ($errors->has('password_confirmation'))
                        <p class="has-text-danger m-b-sm">
                            {{ $errors->first('password_confirmation') }}
                        </p>
                        @endif
                        <p>Please fill the form below to reset your password.</p>
                        <form class="m-t-md m-b-md" method="POST" action="{{ route('password.request') }}">
                            {{ csrf_field() }}
                            <input type="hidden" name="token" value="{{ $token }}">
                            <div class="field">
                                <div class="field">
                                    <input type="email" placeholder="Email Address" name="email" class="input" value="{{ $email or old('email') }}" required autofocus>
                                </div>
                                <div class="field">
                                    <input placeholder="Password" type="password" name="password" class="input" required>
                                </div>
                                <div class="field">
                                    <input placeholder="Confirm Password" type="password" name="password_confirmation" class="input" required>
                                </div>
                            </div>
                            <button type="submit" class="button is-fullwidth">Reset Password</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="box-part right rounded-0 rounded-right">
                <div class="bg rounded-right"></div>
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
