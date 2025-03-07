<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Course;

class SecureFileDownload
{
    public function handle(Request $request, Closure $next)
    {
        $courseId = $request->route('id');
        $user = $request->user();
        $filename = $request->route('filename');

        if (!$courseId || !$user || !$filename) {
            return redirect()->route('courses.index')->with('error', 'Invalid request.');
        }

        $course = Course::find($courseId);
        
        if (!$course || !$user->isEnrolledIn($course)) {
            return redirect()->route('courses.show', $courseId)
                ->with('error', 'You must be enrolled to download course materials.');
        }

        return $next($request);
    }
}
