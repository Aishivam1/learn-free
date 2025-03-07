<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'MentorLink')</title>
    <!-- Load your CSS file -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <!-- FontAwesome and Google Fonts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    @stack('styles')
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const alertBox = document.getElementById("alert-box");
            const closeAlert = document.getElementById("close-alert");

            if (alertBox && closeAlert) {
                closeAlert.addEventListener("click", function() {
                    alertBox.style.display = "none"; // Hide alert on button click
                });

                setTimeout(() => {
                    alertBox.style.display = "none"; // Auto-hide after 5 seconds
                }, 5000);
            }
        });
    </script>
</head>

<body>
    @if(session('success'))
    <div id="alert-box" class="alert alert-success flex justify-between items-center p-4 mb-4 text-green-800 bg-green-100 rounded-lg" role="alert">
        <span>{{ session('success') }}</span>
        <button type="button" id="close-alert" class="text-green-800 font-bold text-lg px-2">&times;</button>
    </div>
@endif

@if(session('warning'))
    <div id="alert-box" class="alert alert-warning flex justify-between items-center p-4 mb-4 text-yellow-800 bg-yellow-100 rounded-lg" role="alert">
        <span>{{ session('warning') }}</span>
        <button type="button" id="close-alert" class="text-yellow-800 font-bold text-lg px-2">&times;</button>
    </div>
@endif
    @if (!isset($hideHeaderFooter) || !$hideHeaderFooter)
        @include('components.header')
    @endif
    <main>
        @yield('content')
    </main>

    @if (!isset($hideHeaderFooter) || !$hideHeaderFooter)
        @include('components.footer')
    @endif
    <!-- Optional: load your JS file -->
    <script src="{{ asset('js/app.js') }}"></script>
    @stack('scripts')
</body>

</html>
