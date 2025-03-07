<?php

namespace App\Http\Controllers\Gamification;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class PointsController extends Controller
{
    private $pointsConfig = [
        'course_completion' => 100,
        'quiz_passed' => 50,
        'discussion_created' => 10,
        'discussion_reply' => 5,
        'course_enrollment' => 20,
        'daily_login' => 5
    ];

    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    public function earnPoints($userId, $actionType)
    {
        if (!array_key_exists($actionType, $this->pointsConfig)) {
            return response()->json([
                'message' => 'Invalid action type'
            ], 400);
        }

        $user = User::findOrFail($userId);
        $pointsToAward = $this->pointsConfig[$actionType];

        // Award points
        $user->points += $pointsToAward;
        $user->save();

        // Clear leaderboard cache
        Cache::tags(['leaderboard'])->flush();

        // Check for badge eligibility
        event(new PointsEarned($user, $pointsToAward));

        return response()->json([
            'message' => "Earned {$pointsToAward} points",
            'total_points' => $user->points
        ]);
    }

    public function getPoints($userId = null)
    {
        $userId = $userId ?? Auth::id();
        
        $user = User::select('id', 'name', 'points')
            ->with(['badges'])
            ->findOrFail($userId);

        $rank = Cache::remember("user_rank_{$userId}", 3600, function() use ($userId) {
            return User::where('points', '>', function($query) use ($userId) {
                $query->select('points')
                    ->from('users')
                    ->where('id', $userId);
            })->count() + 1;
        });

        return response()->json([
            'user' => $user,
            'rank' => $rank,
            'points_config' => $this->pointsConfig
        ]);
    }

    public function getPointsHistory($userId = null)
    {
        $userId = $userId ?? Auth::id();
        
        $history = PointsHistory::where('user_id', $userId)
            ->with('actionable')
            ->latest()
            ->paginate(15);

        return response()->json($history);
    }

    public function getLeaderboard()
    {
        $leaderboard = Cache::tags(['leaderboard'])->remember('leaderboard', 3600, function() {
            return User::select('id', 'name', 'points')
                ->orderBy('points', 'desc')
                ->take(100)
                ->get()
                ->map(function($user, $index) {
                    $user->rank = $index + 1;
                    return $user;
                });
        });

        return response()->json([
            'leaderboard' => $leaderboard
        ]);
    }
}
