<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\User;
use App\Models\Badge;
use App\Models\Material;
use App\Models\Discussion;
use Illuminate\Http\Request;
use App\Models\Enrollment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class HomeController extends Controller
{
    // 🔹 Home Page
    public function index()
    {
        // Get featured courses with mentors and ratings
        $featuredCourses = Cache::remember('featured_courses', 3600, function () {
            return Course::with(['mentor:id,name', 'feedback'])
                ->where('status', 'approved')
                ->take(6)
                ->get();
        });

        // Get top mentors
        $topMentors = Cache::remember('top_mentors', 3600, function () {
            return User::where('role', 'mentor')
                ->withCount(['coursesAsMentor', 'mentorEnrollments'])
                ->orderBy('mentor_enrollments_count', 'desc')
                ->take(4)
                ->get();
        });

        $course = Course::first(); // Get any available course

        // Get user stats if authenticated
        $userStats = null;
        if (Auth::check()) {
            $userStats = [
                'progress' => $this->getUserProgress(),
                'achievements' => $this->getUserAchievements(),
                'recommended' => $this->getRecommendedCourses(),
            ];
        }

        return view('home', compact(
            'featuredCourses',
            'topMentors',
            'course',
            'userStats'
        ));
    }
    public function contact()
    {
        return view('contact');
    }
    // 🔹 Careers Page
    public function careers()
    {
        return view('careers');
    }

    // 🔹 Press Page
    public function press()
    {
        return view('press');
    }

    // 🔹 Help Center Page
    public function helpCenter()
    {
        return view('help-center');
    }

    // 🔹 Terms of Service Page
    public function termsOfService()
    {
        return view('terms-of-service');
    }

    // 🔹 Privacy Policy Page
    public function privacyPolicy()
    {
        return view('privacy-policy');
    }

    // 🔹 FAQ Page
    public function faq()
    {
        return view('faq');
    }
    public function about()
    {
        return view('about');
    }
    // 🔹 User Progress
    private function getUserProgress()
    {
        return Cache::remember('user_progress_' . Auth::id(), 1800, function () {
            $user = Auth::user();
            return [
                'enrolled_courses' => $user->enrollments()->count(),
                'completed_courses' => $user->enrollments()
                    ->whereNotNull('completed_at')
                    ->count(),
                'total_points' => $user->points,
                'badges_earned' => count(config('badges')),
                'next_badge' => $this->getNextBadgeProgress(),
            ];
        });
    }

    // 🔹 User Achievements
    private function getUserAchievements()
    {
        return collect(config('badges'))->take(3);
    }

    // 🔹 Recommended Courses
    private function getRecommendedCourses()
    {
        return Cache::remember('recommended_courses_' . Auth::id(), 1800, function () {
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

    // 🔹 Next Badge Progress
    private function getNextBadgeProgress()
    {
        $userPoints = Auth::user()->points;

        return collect(config('badges'))
            ->where('points_required', '>', $userPoints)
            ->sortBy('points_required')
            ->first();
    }
}
