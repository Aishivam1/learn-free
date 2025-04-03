<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use App\Models\Course;
use Illuminate\Support\Facades\DB;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('layouts.app', function ($view) {
            if (Auth::check()) {
                $user = Auth::user();
                $userCourses = [];

                if ($user->role === 'mentor') {
                    // For mentors, show courses they created
                    $userCourses = Course::where('mentor_id', $user->id)
                        ->where('status', 'approved')
                        ->select('id', 'title')
                        ->get();
                } elseif ($user->role === 'learner') {
                    // For learners, show enrolled courses
                    $enrolledCourseIds = DB::table('enrollments')
                        ->where('user_id', $user->id)
                        ->pluck('course_id');

                    $userCourses = Course::whereIn('id', $enrolledCourseIds)
                        ->where('status', 'approved')
                        ->select('id', 'title')
                        ->get();
                } elseif ($user->role === 'admin') {
                    // For admins, show all courses
                    $userCourses = Course::where('status', 'approved')
                        ->select('id', 'title')
                        ->get();
                }

                $view->with('userCourses', $userCourses);
            }
        });
    }
}
