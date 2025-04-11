<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\GamificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use App\Services\BadgeService;

class AuthController extends Controller
{
    // Show register form with avatar options
    public function registerForm()
    {
        $avatarPath = public_path('avatar/');
        $avatars = File::exists($avatarPath) ? array_diff(scandir($avatarPath), ['.', '..']) : [];
        return view('auth.register', compact('avatars'));
    }

    // Show login form
    public function loginForm()
    {
        return view('auth.login');
    }

    // Handle user registration
    public function register(Request $request)
    {
        $avatarPath = public_path('avatar/');
        $availableAvatars = File::exists($avatarPath) ? array_diff(scandir($avatarPath), ['.', '..']) : [];
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:learner,mentor',
            'avatar' => 'required|in:' . implode(',', $availableAvatars), // Validate from actual files
            'bio' => 'nullable|string|max:1000' // Optional bio field
        ]);

        // Create new user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'avatar' => $request->avatar, // Store selected avatar
            'bio' => $request->bio, // This will be null if not provided

        ]);

        // Award points for registration (first-time registration bonus)
        GamificationService::awardPoints($user, 50);

        // Award badges for first-time registration
        $earnedBadges = json_decode($user->badges, true) ?? [];

        // First registration badge (for new users)
        $earnedBadges[] = [
            "id" => 1,
            "name" => "Newbie",
            "icon" => "newbie.png",
            "description" => "First time registration"
        ];

        // Save updated badges
        $user->update(['badges' => json_encode($earnedBadges)]);

        Auth::login($user);

        return redirect()->route('dashboard')->with('success', 'Registration successful. Welcome!');
    }

    // Handle login with rate limiting
    public function login(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (!Auth::attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            return redirect()->back()
                ->withErrors(['email' => 'Invalid login credentials'])
                ->withInput();
        }
        $request->session()->regenerate();

        $user = Auth::user();
        $today = now()->toDateString(); // Get current date (YYYY-MM-DD)

        // Retrieve session data
        $loginDates = session('login_dates', []); // Get stored login dates (default: empty array)
        $lastLogin = session('last_login_date');

        // Track new login days
        if (!in_array($today, $loginDates)) {
            $loginDates[] = $today;
            session(['login_dates' => $loginDates]);

            // Streak Logic: If last login was yesterday, increase streak; else, reset
            if ($lastLogin && now()->subDay()->toDateString() === $lastLogin) {
                session(['login_streak' => session('login_streak', 0) + 1]);
            } else {
                session(['login_streak' => 1]); // Reset streak
            }

            session(['last_login_date' => $today]); // Update last login date

            // Award badges for login streaks and total login days
            $earnedBadges = json_decode($user->badges, true) ?? [];

            // Award "Streak Holder" badge for 7-day streak
            if (session('login_streak') >= 7 && !array_search("Streak Holder", array_column($earnedBadges, 'name'))) {
                $earnedBadges[] = [
                    "id" => 8,
                    "name" => "Streak Holder",
                    "icon" => "streak_holder.png",
                    "description" => "Logged in for 7 consecutive days"
                ];
            }

            // Award "Committed Learner" badge for 30 total login days
            if (count($loginDates) >= 30 && !array_search("Committed Learner", array_column($earnedBadges, 'name'))) {
                $earnedBadges[] = [
                    "id" => 9,
                    "name" => "Committed Learner",
                    "icon" => "committed_learner.png",
                    "description" => "Logged in for 30 days"
                ];
            }

            // Save updated badges
            $user->update(['badges' => json_encode($earnedBadges)]);
        }

        return redirect()->route('dashboard')->with('success', 'Login successful!');
    }

    // Handle logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Successfully logged out.');
    }

    // Show forgot password form
    public function forgotPasswordForm()
    {
        return view('auth.forgot-password');
    }

    // Handle forgot password request
    public function forgotPassword(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink($request->only('email'));

        return $status === Password::RESET_LINK_SENT
            ? redirect()->back()->with('success', 'Reset password link sent to email.')
            : redirect()->back()->withErrors(['email' => 'Unable to send reset link.']);
    }

    // Show reset password form
    public function resetPasswordForm($token)
    {
        return view('auth.reset-password', ['token' => $token]);
    }

    // Handle password reset
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed|min:8'
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill(['password' => Hash::make($password)])->saveQuietly();
                Auth::login($user); // Log the user in after reset
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('dashboard')->with('success', 'Password reset successful. Welcome back!')
            : redirect()->back()->withErrors(['email' => 'Unable to reset password.']);
    }
}
