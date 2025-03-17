<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'MentorLink')</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">

    <!-- ✅ Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('resources/css/app.css') }}">

    <!-- FontAwesome & Google Fonts -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">

    <!-- SweetAlert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @stack('styles')

</head>

<body class="bg-light">
    <!-- Place alert messages right after opening body tag -->
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show d-flex justify-content-between align-items-center p-3"
            role="alert">
            <span>{{ session('success') }}</span>
            <button type="button" class="btn-close" aria-label="Close"></button>
        </div>
    @endif

    @if (session('warning'))
        <div class="alert alert-warning alert-dismissible fade show d-flex justify-content-between align-items-center p-3"
            role="alert">
            <span>{{ session('warning') }}</span>
            <button type="button" class="btn-close" aria-label="Close"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show d-flex justify-content-between align-items-center p-3"
            role="alert">
            <span>{{ session('error') }}</span>
            <button type="button" class="btn-close" aria-label="Close"></button>
        </div>
    @endif

    @if (session('info'))
        <div class="alert alert-info alert-dismissible fade show d-flex justify-content-between align-items-center p-3"
            role="alert">
            <span>{{ session('info') }}</span>
            <button type="button" class="btn-close" aria-label="Close"></button>
        </div>
    @endif

    @if (!isset($hideHeaderFooter) || !$hideHeaderFooter)
        @include('components.header')
    @endif

    <main class="container min-height-100vh">
        @yield('content')
    </main>

    @if (!isset($hideHeaderFooter) || !$hideHeaderFooter)
        @include('components.footer', ['course' => $course ?? null])
    @endif

    <!-- ✅ Bootstrap JS (Moved to Bottom) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Custom JS -->
    <script src="{{ asset('js/app.js') }}"></script>

    @stack('scripts')

</body>

</html>
