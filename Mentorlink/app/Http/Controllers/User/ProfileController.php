<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;

class ProfileController extends Controller
{
    public function edit()
    {
        $user = auth()->user();

        // Fetch available avatars dynamically from public/avatar/
        $avatarPath = public_path('avatar');
        $avatars = array_diff(scandir($avatarPath), array('..', '.')); // Get all files e

        return view('profile.edit', compact('user', 'avatars'));
    }

    public function viewProfile($userId)
    {
        $user = User::with(['enrollments', 'badges', 'certificates'])
            ->findOrFail($userId);

        return response()->json([
            'user' => $user
        ]);
    }
    public function updateProfile(Request $request)
    {
        // Fetch available avatars dynamically
        $avatarPath = public_path('avatar/');
        $availableAvatars = File::exists($avatarPath) ? array_diff(scandir($avatarPath), ['.', '..']) : [];

        $validator = Validator::make($request->all(), [
            'name' => 'nullable|string|max:255',
            'bio' => 'nullable|string|max:1000',
            'avatar' => 'required|in:' . implode(',', $availableAvatars) // Validate from actual files
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $user = $request->user();

        // Update avatar only if a valid one is selected
        if ($request->has('avatar') && in_array($request->avatar, $availableAvatars)) {
            $user->avatar = $request->avatar;
        }

        $user->fill($request->only(['name', 'bio']));
        $user->save();

        return redirect()->route('profile.edit')->with('success', 'Profile updated successfully.');
    }

    public function updatePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'password' => 'required|string|min:8|confirmed|different:current_password'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user = $request->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'message' => 'Current password is incorrect'
            ], 401);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        return response()->json([
            'message' => 'Password updated successfully'
        ]);
    }
}
