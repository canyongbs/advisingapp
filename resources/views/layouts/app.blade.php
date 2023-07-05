<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="stylesheet" href="{{ asset('css/app.css') }}" />
    <title>{{ __('panel.site_title') }}</title>
</head>

<body class="text-blueGray-700 bg-blueGray-800 antialiased">
    <main>
        @yield('content')
    </main>
</body>

</html>