<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Course;

class QuizAttemptRestriction
{
    public function handle(Request $request, Closure $next)
    {
        $courseId = $request->route('id');
        $user = $request->user();

        if (!$courseId || !$user) {
            return redirect()->route('courses.index')->with('error', 'Course not found.');
        }

        $course = Course::find($courseId);
        
        if (!$course || !$user->hasCompletedCourse($course)) {
            return redirect()->route('courses.show', $courseId)
                ->with('error', 'You must complete the course content before attempting the quiz.');
        }

        return $next($request);
    }
}
