<?php

namespace App\Http\Controllers\Gamification;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class LeaderboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $category = $request->query('category', 'courses'); // Default category is 'courses'

        // Global Leaderboard (Top 10 users by total points)
        $globalLeaderboard = User::select('id', 'name', 'points', 'avatar')
            ->orderByDesc('points')
            ->limit(10)
            ->get();

        // Weekly Leaderboard (Points earned in the current week)
        $weeklyLeaderboard = Cache::remember('weekly_leaderboard', 3600, function () {
            return User::select('id', 'name', 'avatar', 'points')
                ->where('updated_at', '>=', now()->startOfWeek())
                ->orderByDesc('points')
                ->limit(10)
                ->get();
        });


        // Category-based Leaderboard
        $validCategories = ['courses', 'quizzes', 'discussions'];
        if (!in_array($category, $validCategories)) {
            $category = 'courses'; // Default category
        }

        $categoryLeaderboard = Cache::remember("leaderboard_{$category}", 3600, function () use ($category) {
            switch ($category) {
                case 'courses':
                    return User::withCount(['enrolledCourses as total'])
                        ->orderByDesc('total')
                        ->limit(10)
                        ->get();
                case 'quizzes':
                    return User::withCount(['quizAttempts as total' => function ($query) {
                        $query->where('passed', true);
                    }])
                        ->orderByDesc('total')
                        ->limit(10)
                        ->get();
                case 'discussions':
                    return User::withCount(['discussions as total'])
                        ->orderByDesc('total')
                        ->limit(10)
                        ->get();
            }
        });
 
        return view('leaderboard', [
            'globalLeaderboard' => $globalLeaderboard,
            'weeklyLeaderboard' => $weeklyLeaderboard,
            'categoryLeaderboard' => $categoryLeaderboard,
            'category' => $category,
        ]);
    }
}
