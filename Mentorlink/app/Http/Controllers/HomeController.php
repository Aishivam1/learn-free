<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\User;
use App\Models\Badge;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class HomeController extends Controller
{
    public function index()
    {
        // Get featured courses with mentors and ratings
        $featuredCourses = Cache::remember('featured_courses', 3600, function() {
            return Course::with(['mentor:id,name', 'feedback'])
                ->where('status', 'approved')
                ->take(6)
                ->get();
        });

        // Get top mentors
        $topMentors = Cache::remember('top_mentors', 3600, function() {
            return User::where('role', 'mentor')
                ->withCount(['coursesAsMentor', 'mentorEnrollments'])
                ->orderBy('mentor_enrollments_count', 'desc')
                ->take(4)
                ->get();
        });


        // Get top badges with 3D model paths
        // $topBadges = Cache::remember('top_badges', 3600, function() {
        //     return Badge::select('id', 'name', 'description', 'model_path')
        //         ->orderBy('points_required', 'desc')
        //         ->take(3)
        //         ->get();
        // });

        // Get user stats if authenticated
        $userStats = null;
        if (Auth::check()) {
            $userStats = [
                'progress' => $this->getUserProgress(),
                'achievements' => $this->getUserAchievements(),
                'recommended' => $this->getRecommendedCourses()
            ];
        }

        return view('home', compact(
            'featuredCourses',
            'topMentors',
            // 'topBadges',
            'userStats'
        ));
    }

    private function getUserProgress()
    {
        return Cache::remember('user_progress_'.Auth::id(), 1800, function() {
            $user = Auth::user();
            return [
                'enrolled_courses' => $user->enrollments()->count(),
                'completed_courses' => $user->enrollments()
                    ->whereNotNull('completed_at')
                    ->count(),
                'total_points' => $user->points,
'badges_earned' => count(config('badges')),
                'next_badge' => $this->getNextBadgeProgress()
            ];
        });
    }

    private function getUserAchievements()
    {
        return collect(config('badges'))->take(3);

    }

    private function getRecommendedCourses()
    {
        return Cache::remember('recommended_courses_'.Auth::id(), 1800, function() {
            $user = Auth::user();
            $completedCourseIds = $user->enrollments()
                ->whereNotNull('completed_at')
                ->pluck('course_id');

            return Course::with('mentor:id,name')
                ->whereNotIn('id', $completedCourseIds)
                ->where('status', 'approved')
                ->inRandomOrder()
                ->take(3)
                ->get();
        });
    }

    private function getNextBadgeProgress()
    {
        $userPoints = Auth::user()->points;

        return collect(config('badges'))
            ->where('points_required', '>', $userPoints)
            ->sortBy('points_required')
            ->first();
    }
}
