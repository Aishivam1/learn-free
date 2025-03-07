<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Laravel') }} - Mentor</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
    <div id="app">
        @include('components.header')

        <main class="py-4">
            @yield('content')
        </main>

        @include('components.footer')
    </div>
    <script src="{{ asset('js/app.js') }}"></script>
</body>
</html>
