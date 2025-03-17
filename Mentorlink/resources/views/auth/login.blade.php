@extends('layouts.app', ['hideHeaderFooter' => true])

@section('title', 'Login - MentorLink')

@section('content')
    <div class="login-page">
        <!-- 3D Cube Animation -->
        <div class="cube">
            <div class="face front"></div>
            <div class="face back"></div>
            <div class="face right"></div>
            <div class="face left"></div>
            <div class="face top"></div>
            <div class="face bottom"></div>
        </div>
        <div class="container-1">
            <h2>Login to MentorLink</h2>
            <form method="POST" action="{{ route('login') }}">
                @csrf
                <input type="email" name="email" placeholder="Email" required>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit">Login</button>
            </form>
            <div class="register-link">
                Don't have an account? <a href="{{ route('register') }}">Register</a>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
@endpush


@push('scripts')
    <script>

    </script>
@endpush
