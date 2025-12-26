<!DOCTYPE HTML>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="viewport"
        content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, viewport-fit=cover" />
    <title>@yield('title')</title>
    <link rel="stylesheet" type="text/css" href="/assets/styles/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="/assets/styles/style.css">
    <link
        href="https://fonts.googleapis.com/css?family=Roboto:300,300i,400,400i,500,500i,700,700i,900,900i|Source+Sans+Pro:300,300i,400,400i,600,600i,700,700i,900,900i&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="/assets/fonts/css/fontawesome-all.min.css">
    <link rel="manifest" href="/_manifest.json" data-pwa-version="set_in_manifest_and_pwa_js">
    <link rel="apple-touch-icon" sizes="180x180" href="/assets/mobiles/app/icons/icon-192x192.png">
</head>

<body class="theme-light" data-highlight="highlight-red" data-gradient="body-default">

    <div id="page">

        @include('layouts.partials.footer')

        @yield('content')

    </div>

    <script type="text/javascript" src="/assets/scripts/bootstrap.min.js"></script>
    <script type="text/javascript" src="/assets/scripts/custom.js"></script>
    @stack('js')
    @if(session('success'))
    <script>
        if (window.flutter_inappwebview) {
            window.flutter_inappwebview.callHandler('onDataSavedSuccessfully');
        }
    </script>
    @endif
    @if (session('success') || session('status'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Tunggu handler Flutter siap
                setTimeout(function() {
                    if (window.notifyDataSaved) {
                        window.notifyDataSaved();
                    } else if (window.flutter_inappwebview) {
                        window.flutter_inappwebview.callHandler('dataSaved');
                    }
                }, 10);
            });
        </script>
    @endif
</body>
