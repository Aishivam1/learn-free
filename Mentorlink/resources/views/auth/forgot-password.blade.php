@extends('layouts.app', ['hideHeaderFooter' => true])

@section('title', 'Forgot Password - MentorLink')

@section('content')

    <div class="reset-password-page">
        
        <div class="container-1">
            <div class="cube">
                <div class="face front"></div>
                <div class="face back"></div>
                <div class="face right"></div>
                <div class="face left"></div>
                <div class="face top"></div>
                <div class="face bottom"></div>
            </div>
            <h2>Forgot Your Password?</h2>
            <p class="reset-text">Enter your email address and we'll send you a password reset link.</p>

            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}">
                @csrf

                <input type="email" name="email" placeholder="Email Address" value="{{ old('email') }}" required
                    autocomplete="email" autofocus>
                @error('email')
                    <div class="error-message">{{ $message }}</div>
                @enderror

                <button type="submit">Send Reset Link</button>
            </form>
            <div class="login-link">
                Remember your password? <a href="{{ route('login') }}">Login</a>
            </div>
            <div class="login-link">
                Go back to <a href="{{ route('home') }}">home🏠</a>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .min-height-100vh {
            min-height: 0vh;
            display: flex;
            flex-direction: column;
            align-items: center;    
            justify-content: center;
        }

        /* General Page Styling */
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

        /* Reset Password Page Container */
        .reset-password-page {
            max-width: 350px;
            padding: 15px;
        }

        /* Form Container */
        .container-1 {
            background-color: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            width: 350px;
            text-align: center;
            position: relative;
            z-index: 2;
         
        }

        .container-1 h2 {
            margin-bottom: 15px;
            color: #007bff;
            font-size: 18px;
        }

        .container-1 form {
            display: flex;
            flex-direction: column;
        }

        /* Input Fields */
        .container-1 form input {
            margin-bottom: 15px;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 14px;
            background-color: #fff;
            color: #333;
        }

        /* Button */
        .container-1 form button {
            padding: 8px;
            border: none;
            border-radius: 5px;
            background-color: #007bff;
            color: #fff;
            font-size: 14px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .container-1 form button:hover {
            background-color: #0056b3;
        }

        /* Login Link */
        .login-link {
            margin-top: 15px;
            font-size: 12px;
        }

        .login-link a {
            color: #007bff;
            text-decoration: none;
        }

        /* Error Message */
        .error-message {
            color: #dc3545;
            font-size: 12px;
            margin-top: -10px;
            margin-bottom: 10px;
            text-align: left;
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

        /* Additional styles for forgot password page */
        .reset-text {
            color: #666;
            font-size: 14px;
            margin-bottom: 20px;
        }

        .alert {
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 5px;
            font-size: 14px;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
    </style>
@endpush

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // GSAP Animations

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

            // Reset Password Form Animation
            gsap.from(".reset-password-page", {
                x: 100,
                opacity: 0,
                duration: 1,
                ease: "power2.out"
            });

            // Form Inputs Animation
            gsap.from(".container-1 input, .container-1 button", {
                opacity: 0,
                y: 30,
                duration: 0.8,
                ease: "power2.out",
                stagger: 0.2
            });
        });
    </script>
@endpush
