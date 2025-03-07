<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    // Show register form
    public function registerForm()
    {
        return view('auth.register'); // Ensure resources/views/auth/register.blade.php exists
    }

    // Show login form
    public function loginForm()
    {
        return view('auth.login'); // Ensure resources/views/auth/login.blade.php exists
    }

    // Handle registration
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:learner,mentor'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role
        ]);

        Auth::login($user); // Log in the user after registration

        return redirect()->route('dashboard')->with('success', 'Registration successful. Welcome!');
    }

    // Handle login
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        if (!Auth::attempt($request->only('email', 'password'))) {
            return redirect()->back()->withErrors(['email' => 'Invalid login credentials'])->withInput();
        }

        $request->session()->regenerate(); // Prevent session fixation attacks

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
        return view('auth.forgot-password'); // Ensure resources/views/auth/forgot-password.blade.php exists
    }

    // Handle forgot password request
    public function forgotPassword(Request $request)
    {
        $validator = Validator::make($request->all(), ['email' => 'required|email']);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $status = Password::sendResetLink($request->only('email'));

        return $status === Password::RESET_LINK_SENT
            ? redirect()->back()->with('success', 'Reset password link sent to email.')
            : redirect()->back()->withErrors(['email' => 'Unable to send reset link.']);
    }

    // Show reset password form
    public function resetPasswordForm($token)
    {
        return view('auth.reset-password', ['token' => $token]); // Ensure resources/views/auth/reset-password.blade.php exists
    }

    // Handle password reset
    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed|min:8'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill(['password' => Hash::make($password)])->save();
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('success', 'Password reset successful. You can now log in.')
            : redirect()->back()->withErrors(['email' => 'Unable to reset password.']);
    }
}
