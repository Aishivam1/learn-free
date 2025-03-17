<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Session;

class Badge extends Model
{
    protected $fillable = [
        'name',
        'description',
        'icon',
        'points',
        'criteria_type', // 'courses_completed', 'quiz_score', 'discussion_count', 'login_streak', 'total_login_days'
        'criteria_value'
    ];

    protected $casts = [
        'points' => 'integer',
        'criteria_value' => 'integer'
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_badges')->withTimestamps();
    }

    // Badge Criteria Methods
    public static function checkEligibility(User $user, string $type)
    {
        $badge = self::where('criteria_type', $type)->first();
        if (!$badge) return false;

        switch ($type) {
            case 'courses_completed':
                return $user->enrolledCourses()
                    ->wherePivot('progress', 100)
                    ->count() >= $badge->criteria_value;

            case 'quiz_score':
                return $user->quizAttempts()
                    ->where('score', '>=', $badge->criteria_value)
                    ->exists();

            case 'discussion_count':
                return $user->discussions()
                    ->count() >= $badge->criteria_value;

            case 'login_streak': // 🔥 Streak Holder (7 consecutive logins)
                return Session::get('login_streak', 0) >= $badge->criteria_value;

            case 'total_login_days': // 🏆 Committed Learner (30 total logins)
                return count(Session::get('login_dates', [])) >= $badge->criteria_value;

            default:
                return false;
        }
    }

    public static function awardEligibleBadges(User $user)
    {
        $badges = self::all();
        foreach ($badges as $badge) {
            if (self::checkEligibility($user, $badge->criteria_type)) {
                $user->awardBadge($badge);
            }
        }
    }
}
