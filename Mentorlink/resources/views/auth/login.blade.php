@extends('layouts.app', ['hideHeaderFooter' => true])

@section('title', 'Login - MentorLink')

@section('content')
    <div class="cube">
        <div class="face front"></div>
        <div class="face back"></div>
        <div class="face right"></div>
        <div class="face left"></div>
        <div class="face top"></div>
        <div class="face bottom"></div>
    </div>
    <div class="register-page">
        <div class="container-1">
            <h2>Login to MentorLink</h2>
            <form method="POST" action="{{ route('login') }}">
                @csrf
                <input type="email" name="email" placeholder="Email" value="{{ old('email') }}" required>
                @error('email')
                    <div class="error-message">{{ $message }}</div>
                @enderror

                <input type="password" name="password" placeholder="Password" required>
                @error('password')
                    <div class="error-message">{{ $message }}</div>
                @enderror

                <button type="submit">Login</button>
            </form>
            <div class="login-link">
                Don't have an account? <a href="{{ route('register') }}">Register</a>
            </div>
            <a href="{{ route('password.request') }}"class="login-link">
                {{ __('Forgot Your Password?') }}
            </a>
            <div class="login-link">
                Go back to <a href="{{ route('home') }}"> home🏠</a>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500&display=swap');

        body {
            font-family: 'Roboto', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f7fa;
            color: #333;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            perspective: 800px;
        }

        .min-height-100vh {
            min-height: 0vh;
        }

        .register-page {
            max-width: 350px;
            padding: 15px;
        }

        .container {
            background-color: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            width: 350px;
            text-align: center;
            position: relative;
            z-index: 2;
        }

        .container h2 {
            margin-bottom: 15px;
            color: #007bff;
            font-size: 18px;
        }

        .container form {
            display: flex;
            flex-direction: column;
        }

        .container form input {
            margin-bottom: 15px;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 14px;
            background-color: #fff;
            color: #333;
            appearance: none;
        }

        .container form button {
            padding: 8px;
            border: none;
            border-radius: 5px;
            background-color: #007bff;
            color: #fff;
            font-size: 14px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .container form button:hover {
            background-color: #0056b3;
        }

        .error-message {
            color: red;
            font-size: 12px;
            margin-bottom: 10px;
            text-align: left;
        }

        .login-link {
            margin-top: 15px;
            font-size: 12px;
        }

        .login-link a {
            color: #007bff;
            text-decoration: none;
        }

        /* 3D Cube Animation */
        .cube {
            width: 40px;
            height: 40px;
            position: absolute;
            top: 10px;
            right: 10px;
            transform-style: preserve-3d;
            z-index: 1;
        }

        .cube .face {
            position: absolute;
            width: 40px;
            height: 40px;
            background: rgba(0, 123, 255, 0.7);
            border: 1px solid #fff;
        }

        .cube .front {
            transform: translateZ(20px);
        }

        .cube .back {
            transform: rotateY(180deg) translateZ(20px);
        }

        .cube .right {
            transform: rotateY(90deg) translateZ(20px);
        }

        .cube .left {
            transform: rotateY(-90deg) translateZ(20px);
        }

        .cube .top {
            transform: rotateX(90deg) translateZ(20px);
        }

        .cube .bottom {
            transform: rotateX(-90deg) translateZ(20px);
        }
    </style>
@endpush

@push('scripts')
    <!-- GSAP Library -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Floating Cube Animation
            gsap.to(".cube", {
                y: 10,
                repeat: -1,
                yoyo: true,
                duration: 1.5,
                ease: "power1.inOut"
            });

            gsap.to(".cube", {
                rotateX: 360,
                rotateY: 360,
                duration: 5,
                repeat: -1,
                ease: "linear"
            });

            // Login Form Animation
            gsap.from(".register-page", {
                x: 100,
                opacity: 0,
                duration: 1,
                ease: "power2.out"
            });

            // Form Inputs Animation
            gsap.from(".container input, .container button", {
                opacity: 0,
                y: 30,
                duration: 0.8,
                ease: "power2.out",
                stagger: 0.2
            });
        });
    </script>
@endpush
