<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Course;

class EnsureEnrolled
{
    public function handle(Request $request, Closure $next)
    {
        $courseId = $request->route('id');
        $user = $request->user();

        if (!$courseId || !$user) {
            return redirect()->route('courses.index')->with('error', 'Course not found.');
        }

        $course = Course::find($courseId);
        
        if (!$course || !$user->isEnrolledIn($course)) {
            return redirect()->route('courses.show', $courseId)
                ->with('error', 'You must be enrolled in this course to access its content.');
        }

        return $next($request);
    }
}
