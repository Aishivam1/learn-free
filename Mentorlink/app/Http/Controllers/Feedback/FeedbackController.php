<?php

namespace App\Http\Controllers\Feedback;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Feedback;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FeedbackController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    public function listByCourse($courseId)
    {
        $feedback = Feedback::with('user:id,name,avatar')
            ->where('course_id', $courseId)
            ->latest()
            ->paginate(10);

        // Calculate average rating
        $averageRating = Feedback::where('course_id', $courseId)
            ->avg('rating');

        // Get rating distribution
        $ratingDistribution = Feedback::where('course_id', $courseId)
            ->select('rating', DB::raw('count(*) as count'))
            ->groupBy('rating')
            ->get()
            ->pluck('count', 'rating')
            ->toArray();

        return response()->json([
            'feedback' => $feedback,
            'average_rating' => round($averageRating, 1),
            'rating_distribution' => $ratingDistribution
        ]);
    }

    public function submit(Request $request, $courseId)
    {
        $request->validate([
            'rating' => 'required|integer|between:1,5',
            'comment' => 'required|string|max:1000'
        ]);

        // Verify course completion
        $enrollment = Course::findOrFail($courseId)
            ->enrollments()
            ->where('user_id', Auth::id())
            ->where('progress', 100)
            ->firstOrFail();

        // Check for existing feedback
        $existingFeedback = Feedback::where('user_id', Auth::id())
            ->where('course_id', $courseId)
            ->first();

        if ($existingFeedback) {
            return response()->json([
                'message' => 'You have already submitted feedback for this course'
            ], 400);
        }

        $feedback = Feedback::create([
            'user_id' => Auth::id(),
            'course_id' => $courseId,
            'rating' => $request->rating,
            'comment' => $request->comment
        ]);

        // Notify course mentor
        $course = Course::find($courseId);
        $course->mentor->notify(new NewFeedbackReceived($feedback));

        return response()->json([
            'message' => 'Feedback submitted successfully',
            'feedback' => $feedback->load('user:id,name,avatar')
        ], 201);
    }

    public function update(Request $request, $feedbackId)
    {
        $request->validate([
            'rating' => 'required|integer|between:1,5',
            'comment' => 'required|string|max:1000'
        ]);

        $feedback = Feedback::where('user_id', Auth::id())
            ->findOrFail($feedbackId);

        $feedback->update([
            'rating' => $request->rating,
            'comment' => $request->comment
        ]);

        return response()->json([
            'message' => 'Feedback updated successfully',
            'feedback' => $feedback
        ]);
    }

    public function delete($feedbackId)
    {
        $feedback = Feedback::where(function($query) {
                $query->where('user_id', Auth::id())
                    ->orWhere(function($q) {
                        $q->whereHas('course', function($q2) {
                            $q2->where('mentor_id', Auth::id());
                        });
                    });
            })
            ->findOrFail($feedbackId);

        $feedback->delete();

        return response()->json([
            'message' => 'Feedback deleted successfully'
        ]);
    }

    public function report(Request $request, $feedbackId)
    {
        $request->validate([
            'reason' => 'required|string|max:500'
        ]);

        $feedback = Feedback::findOrFail($feedbackId);

        // Create report
        $report = $feedback->reports()->create([
            'user_id' => Auth::id(),
            'reason' => $request->reason
        ]);

        // Notify admins
        event(new FeedbackReported($report));

        return response()->json([
            'message' => 'Feedback reported successfully'
        ]);
    }

    public function getUserFeedback()
    {
        $feedback = Feedback::with(['course:id,title', 'user:id,name,avatar'])
            ->where('user_id', Auth::id())
            ->latest()
            ->paginate(10);

        return response()->json($feedback);
    }
}
