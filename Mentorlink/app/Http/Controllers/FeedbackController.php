<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FeedbackController extends Controller
{
    // ✅ Store Feedback
    public function store(Request $request, $courseId)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string'
        ]);

        Feedback::create([
            'course_id' => $courseId,
            'user_id' => Auth::id(),
            'rating' => $request->rating,
            'comment' => $request->comment
        ]);

        return back()->with('success', 'Feedback submitted successfully!');
    }

    // ✅ Update Feedback
    public function update(Request $request, Feedback $feedback)
    {
        if (Auth::id() !== $feedback->user_id) {
            return back()->with('error', 'Unauthorized!');
        }

        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string'
        ]);

        $feedback->update([
            'rating' => $request->rating,
            'comment' => $request->comment
        ]);

        return back()->with('success', 'Feedback updated successfully!');
    }

    // ✅ Delete Feedback
    public function destroy(Feedback $feedback)
    {
        if (Auth::id() !== $feedback->user_id && !Auth::user()->isAdmin()) {
            return back()->with('error', 'Unauthorized!');
        }

        $feedback->delete();

        return back()->with('success', 'Feedback deleted successfully!');
    }

    // ✅ Report Feedback
    public function report(Feedback $feedback)
    {
        $userId = Auth::id();

        if ($feedback->isReportedBy($userId)) {
            return back()->with('error', 'You have already reported this feedback.');
        }

        $feedback->reportFeedback($userId);

        return back()->with('success', 'Feedback reported successfully!');
    }
}
