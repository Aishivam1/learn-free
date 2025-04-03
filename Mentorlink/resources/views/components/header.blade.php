<header>
    <div class="logo">
        <img src="{{ asset('favicon.png') }}" alt="Favicon" class="favicon">MentorLink
    </div>
    
    <!-- Add the header toggle button -->
    <button class="navbar-toggler" id="navbarToggler">
        <span class="navbar-toggler-icon"></span>
    </button>
    
    <!-- Wrap navigation in a collapsible container -->
    <div class="navbar-collapse" id="navbarContent">
        <nav>
            @guest
                <a href="{{ route('home') }}">Home</a>
            @else
                <a href="{{ route('dashboard') }}">Dashboard</a>
            @endguest
            <a href="{{ route('courses.index') }}">Courses</a>
            <a href="{{ route('leaderboard') }}">Leaderboard</a>
            <a href="{{ route('certificates.index') }}">Certificate</a>
            
            <div class="nav-item dropdown">
                <a href="#" class="dropdown-toggle" id="discussionsDropdownToggle">Discussions</a>
                <div class="dropdown-menu" id="discussionsDropdownMenu">
                    <a class="dropdown-item" href="{{ route('discussions.index') }}">All Discussions</a>
                    @auth
                        @if (Auth::user()->role === 'learner')
                            <a class="dropdown-item" href="{{ route('discussions.my') }}">My Discussions</a>
                            <a class="dropdown-item" href="{{ route('discussions.create') }}">Start New Discussion</a>
                        @endif
                        {{-- @if (isset($userCourses) && count($userCourses) > 0)
                            <div class="dropdown-divider"></div>
                            <h6 class="dropdown-header">Course Discussions</h6>
                            @foreach ($userCourses as $course)
                                <a class="dropdown-item" href="{{ route('discussions.list', $course->id) }}">
                                    {{ $course->title }}
                                </a>
                            @endforeach
                        @endif --}}
                    @endauth
                </div>
            </div>
            
            <a href="{{ route('about') }}">About</a>
        </nav>
        
        <!-- Include auth buttons inside the collapsible container on mobile -->
        <div class="auth-buttons-mobile">
            @guest
                <a class="login" href="{{ route('login') }}">Log in</a>
                <a class="signup" href="{{ route('register') }}">Sign up</a>
            @endguest
        </div>
    </div>
    
    <!-- Keep desktop auth buttons outside -->
    <div class="auth-buttons">
        @guest
            <a class="login" href="{{ route('login') }}">Log in</a>
            <a class="signup" href="{{ route('register') }}">Sign up</a>
        @else
            <div class="profile-dropdown">
                <img src="{{ Auth::user()->avatar ? asset('avatar/' . Auth::user()->avatar) : asset('avatar/default.png') }}"
                    alt="Profile" class="profile-img" id="profileDropdownToggle">
                <div class="dropdown-menu" id="profileDropdownMenu">
                    <a href="{{ route('profile.edit') }}">Profile Settings</a>
                    <a href="{{ route('logout') }}"
                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        Logout
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                </div>
            </div>
        @endguest
    </div>
</header>
@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // GSAP Header Animation
            gsap.from("header", {
                duration: 1,
                y: -50,
                opacity: 0,
                ease: "power3.out"
            });

            // Staggered Nav Links
            gsap.from("nav a", {
                duration: 0.8,
                y: 20,
                opacity: 0,
                stagger: 0.2,
                ease: "power2.out"
            });

            // Logo Animation
            gsap.from(".logo", {
                duration: 1.2,
                opacity: 0,
                scale: 0.8,
                ease: "elastic.out(1, 0.5)"
            });

            // Profile Image Hover Effect
            const profileImg = document.querySelector(".profile-img");
            if (profileImg) {
                profileImg.addEventListener("mouseenter", () => {
                    gsap.to(profileImg, {
                        scale: 1.1,
                        duration: 0.3,
                        ease: "power1.out"
                    });
                });
                profileImg.addEventListener("mouseleave", () => {
                    gsap.to(profileImg, {
                        scale: 1,
                        duration: 0.3,
                        ease: "power1.out"
                    });
                });
            }
        });
    </script>
@endpush