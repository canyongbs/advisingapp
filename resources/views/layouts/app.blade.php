<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1"
    />
    @vite(['resources/css/app.css'])
    <title>{{ __('panel.site_title') }}</title>
</head>

<body class="bg-blueGray-800 text-blueGray-700 antialiased">
    <main>
        @yield('content')
    </main>
</body>

</html>
