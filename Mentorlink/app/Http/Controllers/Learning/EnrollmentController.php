<?php

namespace App\Http\Controllers\Learning;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnrollmentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function enroll($courseId)
    {
        $course = Course::where('status', 'approved')->findOrFail($courseId);

        $existingEnrollment = Enrollment::where('user_id', Auth::id())
            ->where('course_id', $courseId)
            ->first();

        if ($existingEnrollment) {
            return redirect()->route('courses.my')
                ->with('warning', 'You are already enrolled in this course.');
        }

        Enrollment::create([
            'user_id' => Auth::id(),
            'course_id' => $courseId,
            'progress' => 0
        ]);

        return redirect()->route('courses.my')
            ->with('success', 'Successfully enrolled in the course.');
    }

    public function checkEnrollment($courseId)
    {
        $enrollment = Enrollment::where('user_id', Auth::id())
            ->where('course_id', $courseId)
            ->first();

        return response()->json([
            'enrolled' => (bool) $enrollment,
            'enrollment' => $enrollment
        ]);
    }

    public function listEnrolledCourses()
    {
        $enrollments = Enrollment::with(['course' => function ($query) {
            $query->with('mentor');
        }])
            ->where('user_id', Auth::id())
            ->latest()
            ->paginate(10);

        return response()->json($enrollments);
    }

    public function getProgress($courseId)
    {
        $enrollment = Enrollment::where('user_id', Auth::id())
            ->where('course_id', $courseId)
            ->firstOrFail();

        return response()->json([
            'progress' => $enrollment->progress,
            'completed' => (bool) $enrollment->completed_at,
            'completed_at' => $enrollment->completed_at
        ]);
    }
}
