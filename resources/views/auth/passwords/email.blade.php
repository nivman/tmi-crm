@extends('layouts.app')

@section('content')
<div class="login-container">
    <div class="login">
        <div class="login-box">
            <div class="box-part rounded-left">
                <div id="partition-register" class="partition p-b-lg">
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
                        <p class="m-b-lg">Please type your email address below to get reset password link.</p>
                        <form class="m-t-md m-b-md" method="POST" action="{{ route('password.email') }}">
                            {{ csrf_field() }}
                            <div class="form-group">
                                <input type="email" placeholder="Email Address" class="input" name="email" value="{{ old('email') }}" required autofocus>
                            </div>
                            <button type="submit" class="m-t-md button is-fullwidth">Submit</button>
                        </form>
                        <a class="m-t-md p-l-none" href="/login">
                            Back to login
                        </a>
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
        <span class="text-muted">&copy; {{ date('Y') }} {{ config('app.name') }}</span>
    </div>
</footer>
@endsection
