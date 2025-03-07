<?php

namespace App\Http\Controllers\Learning;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VideoController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:sanctum', 'enrolled']);
    }

    public function watch($videoId)
    {
        $course = Course::where('video_url', $videoId)
            ->where('status', 'approved')
            ->firstOrFail();

        $enrollment = Enrollment::where('user_id', Auth::id())
            ->where('course_id', $course->id)
            ->firstOrFail();

        return response()->json([
            'video_url' => $course->video_url,
            'current_progress' => $enrollment->progress
        ]);
    }

    public function trackProgress(Request $request, $courseId, $videoId)
    {
        $request->validate([
            'progress' => 'required|integer|min:0|max:100',
            'timestamp' => 'required|integer'
        ]);

        $enrollment = Enrollment::where('user_id', Auth::id())
            ->where('course_id', $courseId)
            ->firstOrFail();

        // Only update if new progress is greater than current
        if ($request->progress > $enrollment->progress) {
            $enrollment->progress = $request->progress;
            
            // Mark as completed if 100%
            if ($request->progress === 100) {
                $enrollment->completed_at = now();
            }
            
            $enrollment->save();

            // Award points for progress milestones
            if ($request->progress >= 25 && !$enrollment->milestone_25) {
                event(new ProgressMilestoneReached($enrollment, 25));
            } elseif ($request->progress >= 50 && !$enrollment->milestone_50) {
                event(new ProgressMilestoneReached($enrollment, 50));
            } elseif ($request->progress >= 75 && !$enrollment->milestone_75) {
                event(new ProgressMilestoneReached($enrollment, 75));
            } elseif ($request->progress === 100 && !$enrollment->milestone_100) {
                event(new ProgressMilestoneReached($enrollment, 100));
            }
        }

        return response()->json([
            'message' => 'Progress updated successfully',
            'progress' => $enrollment->progress
        ]);
    }
}
