<header>
    <div class="logo"> <img src="{{ asset('favicon.png') }}" alt="Favicon" class="favicon">MentorLink
    </div>
    <nav>
        @guest
            <a href="{{ route('home') }}">Home</a>
        @else
            <a href="{{ route('dashboard') }}">Dashboard</a>
        @endguest
        <a href="{{ route('courses.index') }}">Courses</a>
        <a href="{{ route('leaderboard') }}">Leaderboard</a>
        <a href="{{ route('certificates.index') }}">Certificate</a>
        <a href="{{ route('about') }}">About</a>
    </nav>
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
                    {{-- <a href="{{ route('profile.edit') }}">Change Password</a> --}}
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
