<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Modern Invoicing System') }}</title>
    <link rel="icon" href="/icon-48.png">
    <link rel="manifest" href="/manifest">
    <meta name="theme-color" content="#3273dc">
    <link rel="icon" sizes="48x48" href="/icon-48.png">
    <link rel="icon" sizes="96x96" href="/icon-96.png">
    <link rel="icon" sizes="144x144" href="/icon-144.png">
    <link rel="icon" sizes="192x192" href="/icon-192.png">
    <link rel="icon" sizes="512x512" href="/icon-512.png">
    <link rel="apple-touch-icon" href="/icon-76.png">
    <link rel="apple-touch-icon" sizes="76x76" href="/icon-76.png">
    <link rel="apple-touch-icon" sizes="120x120" href="/icon-120.png">
    <link rel="apple-touch-icon" sizes="152x152" href="/icon-152.png">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="{{ mix('css/loader.css') }}" rel="stylesheet">
    <link href="{{ mix('css/app.css') }}" rel="stylesheet">
</head>

<body>
    <div id="app">
        <div class="preloaderapp" ref="preloaderapp">
            <div class="cbf"></div>
            <div class="has-text-centered" style="margin-top:2rem;font-size:1.2rem;color:#3273dc">
                <span class="wait-title">Please wait,<br></span>
                <span class="wait-message" style="color:#ff3860">Application is being loaded...</span>
            </div>
        </div>
        @yield('content')
        <v-dialog/>
    </div>

    <script src="{{ mix('js/app.js') }}"></script>
</body>

</html>
