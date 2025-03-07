<?php

namespace App\Http\Controllers\Discussion;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Discussion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DiscussionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    public function listByCourse($courseId)
    {
        $discussions = Discussion::with(['user:id,name,avatar', 'replies.user:id,name,avatar'])
            ->where('course_id', $courseId)
            ->whereNull('parent_id') // Get only parent discussions
            ->latest()
            ->paginate(15);

        return response()->json($discussions);
    }

    public function create(Request $request, $courseId)
    {
        $request->validate([
            'message' => 'required|string|max:1000'
        ]);

        $course = Course::findOrFail($courseId);

        $discussion = Discussion::create([
            'course_id' => $courseId,
            'user_id' => Auth::id(),
            'message' => $request->message
        ]);

        // Award points for participation
        event(new DiscussionCreated($discussion));

        return response()->json([
            'message' => 'Discussion created successfully',
            'discussion' => $discussion->load('user:id,name,avatar')
        ], 201);
    }

    public function reply(Request $request, $discussionId)
    {
        $request->validate([
            'message' => 'required|string|max:1000'
        ]);

        $parentDiscussion = Discussion::findOrFail($discussionId);

        $reply = Discussion::create([
            'course_id' => $parentDiscussion->course_id,
            'user_id' => Auth::id(),
            'parent_id' => $discussionId,
            'message' => $request->message
        ]);

        // Award points for participation
        event(new DiscussionReplied($reply));

        // Notify parent discussion creator
        if ($parentDiscussion->user_id !== Auth::id()) {
            $parentDiscussion->user->notify(new NewDiscussionReply($reply));
        }

        return response()->json([
            'message' => 'Reply added successfully',
            'reply' => $reply->load('user:id,name,avatar')
        ], 201);
    }

    public function delete($discussionId)
    {
        $discussion = Discussion::where(function($query) {
                $query->where('user_id', Auth::id())
                    ->orWhereHas('course', function($q) {
                        $q->where('mentor_id', Auth::id());
                    });
            })
            ->findOrFail($discussionId);

        // Delete discussion and its replies
        $discussion->replies()->delete();
        $discussion->delete();

        return response()->json([
            'message' => 'Discussion deleted successfully'
        ]);
    }

    public function report(Request $request, $discussionId)
    {
        $request->validate([
            'reason' => 'required|string|max:500'
        ]);

        $discussion = Discussion::findOrFail($discussionId);

        // Create report
        $report = $discussion->reports()->create([
            'user_id' => Auth::id(),
            'reason' => $request->reason
        ]);

        // Notify admins
        event(new DiscussionReported($report));

        return response()->json([
            'message' => 'Discussion reported successfully'
        ]);
    }

    public function getUserDiscussions()
    {
        $discussions = Discussion::with(['course:id,title', 'user:id,name,avatar'])
            ->where('user_id', Auth::id())
            ->latest()
            ->paginate(10);

        return response()->json($discussions);
    }
}
