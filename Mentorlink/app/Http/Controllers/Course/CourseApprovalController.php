<?php

namespace App\Http\Controllers\Course;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CourseApprovalController extends Controller
{
    public function __construct()
    {
        $this->middleware(middleware: 'auth');
    }

    // List pending courses
    public function listPendingCourses()
    {
        if (auth()->user()->role == 'learner') {
            abort(403, 'Unauthorized access.');
        }

        $pendingCourses = Course::with('mentor')
            ->where('status', 'pending')
            ->latest()
            ->paginate(10);

        return view('courses.pending-courses', compact('pendingCourses'));
    }


    // Approve a course
    public function approve(Course $course)
    {
        if ($course->status !== 'pending') {
            return back()->with('error', 'Course is not in pending state.');
        }

        $course->status = 'approved';

        // Remove or comment out this line:
        // $course->rejection_reason = null;

        $course->save();

        // Notify mentor (if notifications exist)
        // if (method_exists($course->mentor, 'notify')) {
        //     $course->mentor->notify(new CourseApproved($course));
        // }

        return back()->with('success', 'Course approved successfully.');
    }

    // Reject a course with a reason
    public function reject(Request $request, $courseId)
    {
        $course = Course::findOrFail($courseId);

        $course->update([
            'status' => 'rejected',
            'rejection_reason' => $request->input('reason'),
        ]);

        return redirect()->route('courses.index')->with('success', 'Course rejected successfully.');
    }
    public function rejectedCourses()
{
    $mentorId = Auth::id();
    $rejectedCourses = Course::where('mentor_id', $mentorId)
        ->whereNotNull('rejection_reason') // Fetch only rejected courses
        ->get();

    return view('courses.rejected', compact('rejectedCourses'));
}

}
