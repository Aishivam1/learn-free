<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Admin extends Model
{
    // This is a service class that doesn't store data
    // It only provides methods for admin operations

    public static function getPlatformMetrics()
    {
        return [
            'total_users' => User::count(),
            'total_courses' => Course::count(),
            'pending_courses' => Course::pending()->count(),
            'total_enrollments' => Enrollment::count(),
            'completion_rate' => self::getOverallCompletionRate(),
            'quiz_success_rate' => self::getOverallQuizSuccessRate()
        ];
    }

    public static function getOverallCompletionRate()
    {
        $totalEnrollments = Enrollment::count();
        if ($totalEnrollments === 0) return 0;

        $completedEnrollments = Enrollment::whereNotNull('completed_at')->count();
        return ($completedEnrollments / $totalEnrollments) * 100;
    }

    public static function getOverallQuizSuccessRate()
    {
        $totalAttempts = QuizAttempt::count();
        if ($totalAttempts === 0) return 0;

        $passedAttempts = QuizAttempt::where('status', true)->count();
        return ($passedAttempts / $totalAttempts) * 100;
    }

    public static function approveCourse(Course $course)
    {
        $course->update(['status' => 'approved']);
        // Could add notification to mentor here
    }

    public static function rejectCourse(Course $course, $reason)
    {
        $course->update(['status' => 'rejected']);
        // Could add notification to mentor with reason here
    }
}
