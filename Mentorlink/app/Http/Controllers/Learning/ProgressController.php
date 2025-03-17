<?php

namespace App\Http\Controllers\Learning;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Enrollment;

class ProgressController extends Controller
{
    public function updateProgress(Request $request)
    {
        $user = auth()->user();
        $videoId = $request->video_id;
        $courseId = $request->course_id;
        $progress = $request->progress;

        // Debugging - Log request data
        \Log::info("Progress Update Request: ", [
            'user_id' => $user->id,
            'video_id' => $videoId,
            'course_id' => $courseId,
            'progress' => $progress
        ]);

        // Ensure course ID is valid
        if (!$courseId || !$videoId || !$progress) {
            return response()->json(['error' => 'Missing required fields'], 400);
        }

        // Update progress in enrollments table
        $updated = Enrollment::where('user_id', $user->id)
            ->where('course_id', $courseId)
            ->update(['progress' => $progress]);

        if ($updated) {
            return response()->json(['message' => 'Progress updated']);
        } else {
            return response()->json(['error' => 'Failed to update progress'], 500);
        }
    }
}
