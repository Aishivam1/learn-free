@extends('layouts.app', ['hideHeaderFooter' => true])

@section('title', 'Register - MentorLink')

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
        <!-- 3D Cube Animation -->

        <div class="container-1">
            <h2>Create Your MentorLink Account</h2>
            <form method="POST" action="{{ route('register') }}">
                @csrf
                <input type="text" name="name" placeholder="Full Name" value="{{ old('name') }}" required>
                @error('name')
                    <div class="error-message">{{ $message }}</div>
                @enderror

                <input type="email" name="email" placeholder="Email" value="{{ old('email') }}" required>
                @error('email')
                    <div class="error-message">{{ $message }}</div>
                @enderror

                <input type="password" name="password" placeholder="Password" required>
                @error('password')
                    <div class="error-message">{{ $message }}</div>
                @enderror

                <input type="password" name="password_confirmation" placeholder="Confirm Password" required>

                <select name="role" required>
                    <option value="" disabled selected>Select Role</option>
                    <option value="learner" {{ old('role') == 'learner' ? 'selected' : '' }}>Learner</option>
                    <option value="mentor" {{ old('role') == 'mentor' ? 'selected' : '' }}>Mentor</option>
                </select>
                @error('role')
                    <div class="error-message">{{ $message }}</div>
                @enderror
                <textarea name="bio" placeholder="Write a short bio (optional)" maxlength="1000">{{ old('bio') }}</textarea>
                @error('bio')
                    <div class="error-message">{{ $message }}</div>
                @enderror

                <h2>Select an Avatar</h2>

                <!-- Centered Avatar Selection -->
                <div class="avatar-dropdown">
                    <button type="button" class="avatar-btn" id="avatarDropdownBtn">
                        <img id="selectedAvatar" src="{{ asset('avatar/default.png') }}" alt="Select Avatar">
                        <span>Select Avatar</span>
                        <i class="fas fa-chevron-down"></i>
                    </button>

                    <!-- Avatars Grid -->
                    <div class="avatar-options" id="avatarOptions">
                        <div class="avatar-grid">
                            @foreach ($avatars as $avatar)
                                <div class="avatar-item" data-avatar="{{ $avatar }}">
                                    <img src="{{ asset('avatar/' . $avatar) }}" alt="Avatar">
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <input type="hidden" name="avatar" id="selectedAvatarInput" value="{{ old('avatar') }}">
                </div>
                @error('avatar')
                    <div class="error-message">{{ $message }}</div>
                @enderror

                <button type="submit">Register</button>
            </form>
            <div class="login-link">
                Already have an account? <a href="{{ route('login') }}">Login</a>
            </div>
            <div class="login-link">
                Go back to <a href="{{ route('home') }}">home🏠</a>
            </div>
        </div>
    @endsection


    @push('styles')
        <style>
            .min-height-100vh {
                min-height: 0vh;
            }

            .avatar-dropdown {
                position: relative;
                display: flex;
                flex-direction: column;
                align-items: center;
                margin-top: 10px;
                margin-bottom: 10px;
            }

            .avatar-btn {
                display: flex;
                align-items: center;
                justify-content: center;
                width: 180px;
                /* Reduced size */
                padding: 8px;
                border: 2px solid #ccc;
                border-radius: 5px;
                cursor: pointer;
                background: #fff;
            }

            .avatar-btn img {
                width: 35px;
                height: 35px;
                border-radius: 50%;
                margin-right: 8px;
            }

            .avatar-btn i {
                font-size: 12px;
            }

            /* Dropdown */
            .avatar-options {
                position: absolute;
                top: 100%;
                left: 50%;
                transform: translateX(-50%);
                background: white;
                border: 1px solid #ccc;
                box-shadow: 0px 5px 10px rgba(0, 0, 0, 0.1);
                border-radius: 5px;
                display: none;
                z-index: 1000;
                width: 340px;
                /* Increased width to fit 5 columns */
                max-height: 200px;
                /* Reduced height */
                overflow-y: auto;
                padding: 10px;
            }

            /* 5-Column Avatar Grid */
            .avatar-grid {
                display: grid;
                grid-template-columns: repeat(5, 1fr);
                gap: 8px;
                justify-content: center;
                align-items: center;
            }

            .avatar-item {
                display: flex;
                justify-content: center;
                align-items: center;
                cursor: pointer;
                transition: background 0.3s;
                padding: 5px;
            }

            .avatar-item img {
                width: 40px;
                /* Reduced size */
                height: 40px;
                border-radius: 50%;
                border: 2px solid transparent;
            }

            .avatar-item:hover img {
                border-color: #007bff;
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

            /* Register Page Container */
            .register-page {
                max-width: 350px;
                /* Reduced width */
                padding: 15px;
            }

            /* Form Container */
            .container {
                background-color: #fff;
                padding: 30px;
                border-radius: 10px;
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
                width: 350px;
                /* Reduced size */
                text-align: center;
                position: relative;
                z-index: 2;
            }

            .container h2 {
                margin-bottom: 15px;
                color: #007bff;
                font-size: 18px;
                /* Reduced size */
            }

            .container form {
                display: flex;
                flex-direction: column;
            }

            /* Input Fields */
            .container form input,
            .container form select {
                margin-bottom: 15px;
                padding: 8px;
                border: 1px solid #ccc;
                border-radius: 5px;
                font-size: 14px;
                /* Reduced font size */
                background-color: #fff;
                color: #333;
                cursor: pointer;
                appearance: none;
            }

            /* Button */
            .container form button {
                padding: 8px;
                border: none;
                border-radius: 5px;
                background-color: #007bff;
                color: #fff;
                font-size: 14px;
                /* Reduced size */
                cursor: pointer;
                transition: background-color 0.3s ease;
            }

            .container form button:hover {
                background-color: #0056b3;
            }

            /* Login Link */
            .container .login-link {
                margin-top: 15px;
                font-size: 12px;
            }

            .container .login-link a {
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

            /* Prevent cube from shaking during GSAP animation */
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

            /* Reduce avatar dropdown button size */
            .avatar-btn img {
                width: 35px;
                height: 35px;
            }

            /* Adjust avatar grid images */
            .avatar-grid img {
                width: 40px;
                height: 40px;
            }

            /* Reduce 3D cube size */
            .cube {
                width: 40px;
                height: 40px;
            }

            .container form textarea {
                margin-bottom: 15px;
                padding: 8px;
                border: 1px solid #ccc;
                border-radius: 5px;
                font-size: 14px;
                background-color: #fff;
                color: #333;
                resize: vertical;
                min-height: 80px;
            }
        </style>
    @endpush

    @push('scripts')
        <!-- GSAP Library -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>

        <script>
            document.addEventListener("DOMContentLoaded", function() {
                const dropdownBtn = document.getElementById("avatarDropdownBtn");
                const dropdownMenu = document.getElementById("avatarOptions");
                const selectedAvatarImg = document.getElementById("selectedAvatar");
                const selectedAvatarInput = document.getElementById("selectedAvatarInput");

                // Avatar Dropdown Animation
                dropdownBtn.addEventListener("click", function() {
                    if (dropdownMenu.style.display === "block") {
                        gsap.to(dropdownMenu, {
                            scaleY: 0,
                            duration: 0.3,
                            ease: "power2.inOut",
                            onComplete: () => {
                                dropdownMenu.style.display = "none";
                            }
                        });
                    } else {
                        dropdownMenu.style.display = "block";
                        gsap.fromTo(dropdownMenu, {
                            scaleY: 0
                        }, {
                            scaleY: 1,
                            duration: 0.3,
                            ease: "power2.inOut"
                        });
                    }
                });

                document.querySelectorAll(".avatar-item").forEach(item => {
                    item.addEventListener("click", function() {
                        const selectedAvatar = this.getAttribute("data-avatar");
                        selectedAvatarImg.src = "/avatar/" + selectedAvatar;
                        selectedAvatarInput.value = selectedAvatar;
                        gsap.to(dropdownMenu, {
                            scaleY: 0,
                            duration: 0.3,
                            ease: "power2.inOut",
                            onComplete: () => {
                                dropdownMenu.style.display = "none";
                            }
                        });
                    });
                });

                // Close dropdown when clicking outside
                document.addEventListener("click", function(event) {
                    if (!dropdownBtn.contains(event.target) && !dropdownMenu.contains(event.target)) {
                        gsap.to(dropdownMenu, {
                            scaleY: 0,
                            duration: 0.3,
                            ease: "power2.inOut",
                            onComplete: () => {
                                dropdownMenu.style.display = "none";
                            }
                        });
                    }
                });

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

                // Register Form Animation
                gsap.from(".register-page", {
                    x: 100,
                    opacity: 0,
                    duration: 1,
                    ease: "power2.out"
                });

                // Form Inputs Animation
                gsap.from(".container-1 input, .container-1 select, .container-1 button,.container-1 textarea", {
                    opacity: 0,
                    y: 30,
                    duration: 0.8,
                    ease: "power2.out",
                    stagger: 0.2
                });
            });
        </script>
    @endpush
