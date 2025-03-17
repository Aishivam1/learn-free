<?php

namespace App\Http\Controllers\Gamification;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Badge;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BadgeController extends Controller
{


    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    public function userBadges()
    {
        $user = Auth::user();
        $badges = json_decode($user->badges, true) ?? [];
        return view('gamification.userBadges', compact('badges'));
    }
}
