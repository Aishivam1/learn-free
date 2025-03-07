<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\VideoProgress;

class NonSkippableVideo
{
    public function handle(Request $request, Closure $next)
    {
        $courseId = $request->route('id');
        $user = $request->user();
        $videoPosition = $request->input('position');

        if (!$courseId || !$user || !$videoPosition) {
            return response()->json(['error' => 'Invalid request.'], 400);
        }

        $lastProgress = VideoProgress::where('user_id', $user->id)
            ->where('course_id', $courseId)
            ->latest()
            ->first();

        if ($lastProgress && $videoPosition > $lastProgress->position + 30) { // Allow 30 seconds buffer
            return response()->json([
                'error' => 'Video skipping is not allowed.',
                'last_position' => $lastProgress->position
            ], 403);
        }

        return $next($request);
    }
}
