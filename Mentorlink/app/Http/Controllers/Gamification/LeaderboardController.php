<?php

namespace App\Http\Controllers\Gamification;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class LeaderboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    // Global Leaderboard (renamed to index() so the route works)
    public function index()
    {
        $leaderboard = User::select('id', 'name', 'points')
            ->orderByDesc('points')
            ->limit(10) // Show only top 10
            ->get()
            ->map(function ($user, $index) {
                return [
                    'rank'     => $index + 1,
                    'user_id'  => $user->id,
                    'name'     => $user->name,
                    'points'   => $user->points,
                    'avatar'   => $user->avatar
                ];
            });

        $user = auth()->user();
        $userRank = $user ? User::where('points', '>', $user->points)->count() + 1 : null;

        return view('leaderboard', [
            'leaderboard' => $leaderboard,
            'userRank'    => $userRank,
            'totalUsers'  => User::count()
        ]);
    }

    // Weekly Leaderboard Without `points_history` Table
    public function getWeeklyLeaderboard()
    {
        $weeklyLeaderboard = Cache::remember('weekly_leaderboard', 3600, function() {
            return User::select('id', 'name', DB::raw('SUM(points) as weekly_points'))
                ->where('updated_at', '>=', now()->startOfWeek())
                ->groupBy('id', 'name')
                ->orderByDesc('weekly_points')
                ->limit(50)
                ->get();
        });

        return response()->json(['weekly_leaderboard' => $weeklyLeaderboard]);
    }

    // Category-based Leaderboard
    public function getCategoryLeaderboard($category)
    {
        $validCategories = ['courses', 'quizzes', 'discussions'];

        if (!in_array($category, $validCategories)) {
            return response()->json(['message' => 'Invalid category'], 400);
        }

        $categoryLeaderboard = Cache::remember("leaderboard_{$category}", 3600, function() use ($category) {
            switch ($category) {
                case 'courses':
                    return User::withCount(['enrolledCourses as total'])
                        ->orderByDesc('total')
                        ->limit(50)
                        ->get();

                case 'quizzes':
                    return User::withCount(['quizAttempts as total' => function($query) {
                            $query->where('passed', true);
                        }])
                        ->orderByDesc('total')
                        ->limit(50)
                        ->get();

                case 'discussions':
                    return User::withCount(['discussions as total'])
                        ->orderByDesc('total')
                        ->limit(50)
                        ->get();
            }
        });

        return response()->json([
            'category' => $category,
            'leaderboard' => $categoryLeaderboard
        ]);
    }

    // Get User Stats Without `leaderboards` Table
    public function getUserStats($userId = null)
    {
        $userId = $userId ?? auth()->id();

        $stats = Cache::remember("user_stats_{$userId}", 3600, function() use ($userId) {
            $user = User::with([
                'enrolledCourses',
                'quizAttempts' => function($query) {
                    $query->where('passed', true);
                },
                'discussions',
                'badges'
            ])->findOrFail($userId);

            return [
                'courses_completed' => $user->enrolledCourses->count(),
                'quizzes_passed'    => $user->quizAttempts->count(),
                'discussions_created' => $user->discussions->count(),
                'badges_earned'     => $user->badges->count(),
                'total_points'      => $user->points,
                'global_rank'       => User::where('points', '>', $user->points)->count() + 1
            ];
        });

        return response()->json($stats);
    }
}
