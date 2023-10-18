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
        <form-embed/>
        <script src="{{ url('js/widgets/form/assist-form-widget.js?') . \Illuminate\Support\Arr::query(['form' => '9a646ffa-407b-4d49-83b3-8a393955b666']) }}"></script>
    </div>
</body>

</html>
