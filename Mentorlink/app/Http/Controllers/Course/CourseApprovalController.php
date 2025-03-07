<?php

namespace App\Http\Controllers\Course;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;

class CourseApprovalController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin']);
    }

    public function listPendingCourses()
    {
        $pendingCourses = Course::with('mentor')
            ->where('status', 'pending')
            ->latest()
            ->paginate(10);

        return response()->json($pendingCourses);
    }

    public function approve($courseId)
    {
        $course = Course::findOrFail($courseId);

        if ($course->status !== 'pending') {
            return response()->json([
                'message' => 'Course is not in pending state'
            ], 400);
        }

        $course->status = 'approved';
        $course->save();

        // Notify mentor about course approval
        $course->mentor->notify(new CourseApproved($course));

        return response()->json([
            'message' => 'Course approved successfully',
            'course' => $course
        ]);
    }

    public function reject($courseId, Request $request)
    {
        $request->validate([
            'reason' => 'required|string|max:1000'
        ]);

        $course = Course::findOrFail($courseId);

        if ($course->status !== 'pending') {
            return response()->json([
                'message' => 'Course is not in pending state'
            ], 400);
        }

        $course->status = 'rejected';
        $course->rejection_reason = $request->reason;
        $course->save();

        // Notify mentor about course rejection
        $course->mentor->notify(new CourseRejected($course));

        return response()->json([
            'message' => 'Course rejected successfully',
            'course' => $course
        ]);
    }
}
