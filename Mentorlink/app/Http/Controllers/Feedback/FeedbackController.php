<?php

namespace App\Http\Controllers\Feedback;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Support\Facades\DB;
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

    public function store(Request $request, $courseId)
    {
        $request->validate([
            'rating' => 'required|integer|between:1,5',
            'comment' => 'required|string|max:1000'
        ], [
            'rating.required' => 'The rating field is required.',
            'comment.required' => 'The comment field is required.',
            'comment.string' => 'The comment must be a string.',
            'comment.max' => 'The comment must not exceed 1000 characters.'
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
            $existingFeedback->update([
                'rating' => $request->rating,
                'comment' => $request->comment
            ]);

            return redirect()->route('courses.show', $courseId)
                ->with('message', 'Feedback updated successfully');
        }

        $feedback = Feedback::create([
            'user_id' => Auth::id(),
            'course_id' => $courseId,
            'rating' => $request->rating,
            'comment' => $request->comment
        ]);

        return redirect()->route('courses.show', $courseId)
            ->with('message', 'Feedback submitted successfully');
    }


    public function update(Request $request, $feedbackId)
    {
        $request->validate([
            'rating' => 'required|integer|between:1,5',
            'comment' => 'required|string|max:1000'
        ], [
            'rating.required' => 'The rating field is required.',
            'rating.integer' => 'The rating must be an integer.',
            'rating.between' => 'The rating must be between 1 and 5.',
            'comment.required' => 'The comment field is required.',
            'comment.string' => 'The comment must be a string.',
            'comment.max' => 'The comment must not exceed 1000 characters.'
        ]);
        $feedback = Feedback::where('user_id', Auth::id())->findOrFail($feedbackId);

        $feedback->update([
            'rating' => $request->rating,
            'comment' => $request->comment
        ]);

        return redirect()->back()->with('success', 'Feedback updated successfully');
    }


    public function destroy($feedbackId)
    {
        $feedback = Feedback::where(function ($query) {
            $query->where('user_id', Auth::id())
                ->orWhere(function ($q) {
                    $q->whereHas('course', function ($q2) {
                        $q2->where('mentor_id', Auth::id());
                    });
                });
        })->findOrFail($feedbackId);

        $feedback->delete();

        return redirect()->back()->with('success', 'Feedback deleted successfully');
    }

    public function report(Request $request, $feedbackId)
    {
        $request->validate([
            'reason' => 'required|string|max:500'
        ], [
            'reason.required' => 'The reason field is required.',
            'reason.string' => 'The reason must be a string.',
            'reason.max' => 'The reason must not exceed 500 characters.'
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
