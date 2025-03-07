<?php

namespace App\Http\Controllers\Gamification;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Badge;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BadgeController extends Controller
{
    private $badgeRules = [
        'course_master' => [
            'points' => 1000,
            'description' => 'Complete 10 courses'
        ],
        'quiz_champion' => [
            'points' => 500,
            'description' => 'Pass 5 quizzes with 100% score'
        ],
        'active_learner' => [
            'points' => 200,
            'description' => 'Login for 7 consecutive days'
        ],
        'discussion_expert' => [
            'points' => 300,
            'description' => 'Create 20 meaningful discussions'
        ],
        'helpful_mentor' => [
            'points' => 1500,
            'description' => 'Help 50 learners complete courses'
        ]
    ];

    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    public function assignBadge($userId, $badgeType)
    {
        if (!array_key_exists($badgeType, $this->badgeRules)) {
            return response()->json([
                'message' => 'Invalid badge type'
            ], 400);
        }

        $user = User::findOrFail($userId);
        
        // Check if user already has this badge
        if ($user->badges()->where('type', $badgeType)->exists()) {
            return response()->json([
                'message' => 'Badge already earned'
            ], 400);
        }

        // Verify badge requirements
        if (!$this->verifyBadgeRequirements($user, $badgeType)) {
            return response()->json([
                'message' => 'Badge requirements not met'
            ], 400);
        }

        // Award badge
        $badge = Badge::create([
            'user_id' => $userId,
            'type' => $badgeType,
            'awarded_at' => now()
        ]);

        // Notify user
        $user->notify(new BadgeEarned($badge));

        return response()->json([
            'message' => 'Badge awarded successfully',
            'badge' => $badge
        ]);
    }

    private function verifyBadgeRequirements($user, $badgeType)
    {
        switch ($badgeType) {
            case 'course_master':
                return $user->completedCourses()->count() >= 10;
            
            case 'quiz_champion':
                return $user->quizAttempts()
                    ->where('score', 100)
                    ->count() >= 5;
            
            case 'active_learner':
                return $user->loginStreak >= 7;
            
            case 'discussion_expert':
                return $user->discussions()->count() >= 20;
            
            case 'helpful_mentor':
                return $user->mentorCourses()
                    ->whereHas('enrollments', function($query) {
                        $query->where('progress', 100);
                    })
                    ->count() >= 50;
            
            default:
                return false;
        }
    }

    public function listBadges($userId = null)
    {
        $userId = $userId ?? Auth::id();
        
        $user = User::with('badges')
            ->findOrFail($userId);

        $earnedBadges = $user->badges->pluck('type')->toArray();
        
        $allBadges = collect($this->badgeRules)->map(function($rule, $type) use ($earnedBadges) {
            return [
                'type' => $type,
                'description' => $rule['description'],
                'points_required' => $rule['points'],
                'earned' => in_array($type, $earnedBadges)
            ];
        });

        return response()->json([
            'badges' => $allBadges
        ]);
    }

    public function getBadgeProgress($userId = null)
    {
        $userId = $userId ?? Auth::id();
        $user = User::findOrFail($userId);

        $progress = [];
        foreach ($this->badgeRules as $type => $rule) {
            $progress[$type] = [
                'description' => $rule['description'],
                'points_required' => $rule['points'],
                'current_progress' => $this->calculateBadgeProgress($user, $type)
            ];
        }

        return response()->json([
            'badge_progress' => $progress
        ]);
    }

    private function calculateBadgeProgress($user, $badgeType)
    {
        switch ($badgeType) {
            case 'course_master':
                return [
                    'completed' => $user->completedCourses()->count(),
                    'required' => 10
                ];
            
            case 'quiz_champion':
                return [
                    'completed' => $user->quizAttempts()
                        ->where('score', 100)
                        ->count(),
                    'required' => 5
                ];
            
            // Add other badge progress calculations
            
            default:
                return null;
        }
    }
}
