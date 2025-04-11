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
            <!-- Home/Dashboard Dropdown -->
            <div class="nav-item dropdown">
                @guest
                    <a href="#" class="dropdown-toggle" id="homeDropdownToggle">Home</a>
                    <div class="dropdown-menu" id="homeDropdownMenu">
                        <a class="dropdown-item" href="{{ route('home') }}">Home</a>

                    </div>
                @else
                    <a href="#" class="dropdown-toggle" id="dashboardDropdownToggle">Dashboard</a>
                    <div class="dropdown-menu" id="dashboardDropdownMenu">
                        <a class="dropdown-item" href="{{ route('dashboard') }}">My Dashboard</a>
                        <a class="dropdown-item" href="{{ route('profile.edit') }}">Profile Settings</a>
                    </div>
                @endguest
            </div>

            <!-- Courses Dropdown -->
            <div class="nav-item dropdown">
                <a href="#" class="dropdown-toggle" id="coursesDropdownToggle">Courses</a>
                <div class="dropdown-menu" id="coursesDropdownMenu">
                    <a class="dropdown-item" href="{{ route('courses.index') }}">All Courses</a>
                    @auth
                        @if (Auth::user()->role === 'learner')
                            <a class="dropdown-item" href="{{ route('courses.my') }}">My Courses</a>
                        @endif
                        @if (Auth::user()->role === 'mentor')
                            <a class="dropdown-item" href="{{ route('courses.create') }}">Create Course</a>
                            <a class="dropdown-item" href="{{ route('courses.rejected') }}">Rejected Courses</a>
                        @endif
                    @endauth
                </div>
            </div>

            <!-- Leaderboard Dropdown -->
            <div class="nav-item dropdown">
                <a href="#" class="dropdown-toggle" id="leaderboardDropdownToggle">Leaderboard</a>
                <div class="dropdown-menu" id="leaderboardDropdownMenu">
                    <a class="dropdown-item" href="{{ route('leaderboard') }}">Leaderboard</a>
                    @auth
                        @if (Auth::user()->role !== 'admin')
                            <a class="dropdown-item" href="{{ route('badges.index') }}">My Badges</a>
                        @endif
                    @endauth
                </div>
            </div>
            @auth
                @if (Auth::user()->role === 'lerner')
                    <!-- Certificates Dropdown -->
                    <div class="nav-item dropdown">
                        <a href="#" class="dropdown-toggle" id="certificatesDropdownToggle">Certificate</a>
                        <div class="dropdown-menu" id="certificatesDropdownMenu">
                            <a class="dropdown-item" href="{{ route('certificates.index') }}">My Certificates</a>
                        </div>
                    </div>
                @endif
            @endauth
            @auth
                @if (Auth::user()->role === 'admin')
                    <!-- Certificates Dropdown -->
                    <div class="nav-item dropdown">
                        <a href="#" class="dropdown-toggle" id="certificatesDropdownToggle">All users</a>
                        <div class="dropdown-menu" id="certificatesDropdownMenu">
                            <a class="dropdown-item" href="{{ route('admin.users') }}">Users</a>
                        </div>
                    </div>
                @endif
            @endauth

            <!-- Discussions Dropdown (Existing) -->
            <div class="nav-item dropdown">
                <a href="#" class="dropdown-toggle" id="discussionsDropdownToggle">Discussions</a>
                <div class="dropdown-menu" id="discussionsDropdownMenu">
                    <a class="dropdown-item" href="{{ route('discussions.index') }}">All Discussions</a>
                    @auth
                        @if (Auth::user()->role === 'learner')
                            <a class="dropdown-item" href="{{ route('discussions.my') }}">My Discussions</a>
                            <a class="dropdown-item" href="{{ route('discussions.create') }}">Start New Discussion</a>
                        @endif
                    @endauth
                </div>
            </div>

            <!-- About Dropdown -->
            <div class="nav-item dropdown">
                <a href="#" class="dropdown-toggle" id="aboutDropdownToggle">About</a>
                <div class="dropdown-menu" id="aboutDropdownMenu">
                    <a class="dropdown-item" href="{{ route('about') }}">About Us</a>
                    <a class="dropdown-item" href="{{ route('careers') }}">Careers</a>
                    <a class="dropdown-item" href="{{ route('press') }}">Press</a>
                    <a class="dropdown-item" href="{{ route('terms-of-service') }}">Terms of Service</a>
                    <a class="dropdown-item" href="{{ route('privacy-policy') }}">Privacy Policy</a>
                    <a class="dropdown-item" href="{{ route('contact') }}">Contact Us</a>
                    <a class="dropdown-item" href="{{ route('help-center') }}">Help Center</a>
                    <a class="dropdown-item" href="{{ route('faq') }}">FAQ</a>
                </div>
            </div>
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
            gsap.from(".nav-item .dropdown-toggle", {
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

            // Setup dropdowns
            const dropdownToggles = document.querySelectorAll('.dropdown-toggle');
            dropdownToggles.forEach(toggle => {
                toggle.addEventListener('click', function(e) {
                    e.preventDefault();
                    const dropdownId = this.id.replace('Toggle', 'Menu');
                    const dropdown = document.getElementById(dropdownId);

                    // Close all other dropdowns first
                    document.querySelectorAll('.dropdown-menu').forEach(menu => {
                        if (menu.id !== dropdownId) {
                            menu.classList.remove('show');
                        }
                    });

                    // Toggle current dropdown
                    dropdown.classList.toggle('show');
                });
            });

            // Close dropdowns when clicking outside
            document.addEventListener('click', function(e) {
                if (!e.target.closest('.nav-item') && !e.target.closest('.profile-dropdown')) {
                    document.querySelectorAll('.dropdown-menu').forEach(menu => {
                        menu.classList.remove('show');
                    });
                }
            });
        });
    </script>
@endpush
