<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Point extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'points',
        'reason',
        'metadata'
    ];

    protected $casts = [
        'points' => 'integer',
        'metadata' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    const REASONS = [
        'quiz_pass' => 50,
        'course_completion' => 100,
        'discussion_created' => 10,
        'discussion_reply' => 5,
        'course_enrollment' => 20,
        'daily_login' => 5,
        'feedback_submitted' => 10,
        'badge_earned' => 30
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopeThisWeek($query)
    {
        return $query->whereBetween('created_at', [
            now()->startOfWeek(),
            now()->endOfWeek()
        ]);
    }

    public function scopeThisMonth($query)
    {
        return $query->whereBetween('created_at', [
            now()->startOfMonth(),
            now()->endOfMonth()
        ]);
    }

    // Helper methods
    public static function award($userId, $reason, $metadata = [])
    {
        if (!array_key_exists($reason, self::REASONS)) {
            throw new \InvalidArgumentException('Invalid point reason');
        }

        $points = self::REASONS[$reason];

        $pointRecord = self::create([
            'user_id' => $userId,
            'points' => $points,
            'reason' => $reason,
            'metadata' => $metadata
        ]);

        // Update user's total points
        $user = User::find($userId);
        $user->increment('points', $points);

        // Check for badge eligibility
        event(new PointsEarned($user, $points, $reason));

        return $pointRecord;
    }

    public function getPointsDescription()
    {
        $descriptions = [
            'quiz_pass' => 'Passed a quiz',
            'course_completion' => 'Completed a course',
            'discussion_created' => 'Started a discussion',
            'discussion_reply' => 'Replied to a discussion',
            'course_enrollment' => 'Enrolled in a course',
            'daily_login' => 'Daily login bonus',
            'feedback_submitted' => 'Submitted course feedback',
            'badge_earned' => 'Earned a badge'
        ];

        return $descriptions[$this->reason] ?? 'Points earned';
    }

    public static function getLeaderboard($timeframe = 'all')
    {
        $query = self::query();

        if ($timeframe === 'week') {
            $query->thisWeek();
        } elseif ($timeframe === 'month') {
            $query->thisMonth();
        }

        return $query->select('user_id')
            ->selectRaw('SUM(points) as total_points')
            ->groupBy('user_id')
            ->orderByDesc('total_points')
            ->with('user:id,name,avatar')
            ->take(100)
            ->get();
    }
}
