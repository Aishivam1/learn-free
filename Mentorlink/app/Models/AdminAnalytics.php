<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class AdminAnalytics extends Model
{
    use HasFactory;

    protected $fillable = [
        'total_users',
        'total_courses',
        'total_enrollments',
        'quiz_pass_rate',
        'active_users_daily',
        'active_users_weekly',
        'active_users_monthly',
        'revenue_daily',
        'revenue_monthly',
        'course_completion_rate'
    ];

    protected $casts = [
        'quiz_pass_rate' => 'float',
        'course_completion_rate' => 'float',
        'revenue_daily' => 'decimal:2',
        'revenue_monthly' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Helper methods
    public static function updateDailyMetrics()
    {
        $metrics = self::calculateMetrics();
        
        self::create($metrics);

        Cache::tags(['admin_analytics'])->flush();

        return $metrics;
    }

    private static function calculateMetrics()
    {
        return [
            'total_users' => User::count(),
            'total_courses' => Course::count(),
            'total_enrollments' => Enrollment::count(),
            'quiz_pass_rate' => self::calculateQuizPassRate(),
            'active_users_daily' => self::getActiveUsers('daily'),
            'active_users_weekly' => self::getActiveUsers('weekly'),
            'active_users_monthly' => self::getActiveUsers('monthly'),
            'revenue_daily' => self::calculateRevenue('daily'),
            'revenue_monthly' => self::calculateRevenue('monthly'),
            'course_completion_rate' => self::calculateCompletionRate()
        ];
    }

    private static function calculateQuizPassRate()
    {
        $totalAttempts = QuizAttempt::count();
        if ($totalAttempts === 0) return 0;

        $passedAttempts = QuizAttempt::where('passed', true)->count();
        return ($passedAttempts / $totalAttempts) * 100;
    }

    private static function getActiveUsers($period)
    {
        $date = match($period) {
            'daily' => now()->subDay(),
            'weekly' => now()->subWeek(),
            'monthly' => now()->subMonth(),
            default => now()->subDay()
        };

        return User::where('last_login_at', '>=', $date)->count();
    }

    private static function calculateRevenue($period)
    {
        $date = match($period) {
            'daily' => now()->subDay(),
            'monthly' => now()->subMonth(),
            default => now()->subDay()
        };

        return Payment::where('created_at', '>=', $date)
            ->where('status', 'completed')
            ->sum('amount');
    }

    private static function calculateCompletionRate()
    {
        $totalEnrollments = Enrollment::count();
        if ($totalEnrollments === 0) return 0;

        $completedEnrollments = Enrollment::where('progress', 100)->count();
        return ($completedEnrollments / $totalEnrollments) * 100;
    }

    // Analytics retrieval methods
    public static function getDailyStats()
    {
        return Cache::tags(['admin_analytics'])->remember('daily_stats', 3600, function() {
            return self::latest()->first();
        });
    }

    public static function getGrowthStats()
    {
        return Cache::tags(['admin_analytics'])->remember('growth_stats', 3600, function() {
            $current = self::latest()->first();
            $previous = self::where('created_at', '<', now()->subDay())
                ->latest()
                ->first();

            if (!$current || !$previous) return [];

            return [
                'user_growth' => self::calculateGrowth(
                    $previous->total_users,
                    $current->total_users
                ),
                'course_growth' => self::calculateGrowth(
                    $previous->total_courses,
                    $current->total_courses
                ),
                'enrollment_growth' => self::calculateGrowth(
                    $previous->total_enrollments,
                    $current->total_enrollments
                ),
                'revenue_growth' => self::calculateGrowth(
                    $previous->revenue_monthly,
                    $current->revenue_monthly
                )
            ];
        });
    }

    private static function calculateGrowth($previous, $current)
    {
        if ($previous == 0) return 100;
        return (($current - $previous) / $previous) * 100;
    }
}
