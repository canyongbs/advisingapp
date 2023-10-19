<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1"
    >

    <title>Form Test</title>
</head>

<body class="antialiased">
    <div>
        <form-embed></form-embed>
        <script src="{{ url('js/widgets/form/assist-form-widget.js?') . \Illuminate\Support\Arr::query(['form' => '9a66d901-2e2a-44f6-9ce6-df70240ea66c']) }}"></script>
    </div>
</body>

</html>
