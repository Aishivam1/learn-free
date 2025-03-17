<?php

namespace App\Http\Controllers\Discussion;

use App\Services\GamificationService;
use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Discussion;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Make sure you have this at the top of your file
use Illuminate\Support\Facades\Log;
use App\Services\BadgeService;

class DiscussionController extends Controller
{
    public function __construct()
    {
        $this->middleware(middleware: 'auth');
    }
    public function listByCourse($courseId)
    {
        $course = Course::findOrFail($courseId); // Fetch course details

        $discussions = Discussion::where('course_id', $courseId)
            ->whereNull('parent_id')
            ->with(['user:id,name', 'replies.user:id,name'])
            ->latest()
            ->paginate(15);

        return view('discussions.index', compact('discussions', 'course'));
    }
    public function showCreateForm($courseId)
    {
        $course = Course::find($courseId);

        if (!$course) {
            dd("Course not found!", $courseId);
        }

        return view('discussions.create', compact('course'));
    }
    public function create(Request $request, $courseId)
    {
        $request->validate([
            'message' => 'required|string|max:1000'
        ]);

        $course = Course::findOrFail($courseId);
        $user = Auth::user();
        $discussion = Discussion::create([
            'course_id' => $courseId,
            'user_id' => Auth::id(),
            'message' => $request->message
        ]);

        // Award points for participation
        GamificationService::awardPoints($user, 20);

        // Get the current badges
        $earnedBadges = json_decode($user->badges, true) ?? [];
        // Check for engagement badges based on discussion creation
        $discussionCount = Discussion::where('user_id', $user->id)->count();
        if ($discussionCount >= 10 && !array_search("Active Contributor", array_column($earnedBadges, 'name'))) {
            $earnedBadges[] = [
                "id" => 6,
                "name" => "Active Contributor",
                "icon" => "active_contributor.png",
                "description" => "Posted 10 discussions"
            ];
        }
        $user->update(['badges' => json_encode($earnedBadges)]);
        return back()->with('success', 'Discussion created successfully.');
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
            'parent_id' => $discussionId, // Nested reply
            'message' => $request->message
        ]);
        $user = Auth::user();

        // Award points for replying
        GamificationService::awardPoints($user, 10);


        return back()->with('success', 'Reply added successfully.');
    }


    public function likeDiscussion($discussionId)
    {
        $discussion = Discussion::findOrFail($discussionId);
        $likes = json_decode($discussion->likes ?? '[]', true); // Decode likes JSON

        $userId = auth()->id();
        if (in_array($userId, $likes)) {
            // Unlike if already liked
            $likes = array_values(array_diff($likes, [$userId]));
        } else {
            // Add like
            $likes[] = $userId;
        }
        $user = Auth::user();

        // Save updated likes (JSON format)
        $discussion->update(['likes' => json_encode($likes)]);
        $earnedBadges = json_decode($user->badges, true) ?? [];
        $likeCount = Discussion::where('user_id', $user->id)
            ->whereJsonContains('likes', Auth::id())
            ->count();

        if ($likeCount >= 50 && !array_search("Community Helper", array_column($earnedBadges, 'name'))) {
            $earnedBadges[] = [
                "id" => 7,
                "name" => "Community Helper",
                "icon" => "community_helper.png",
                "description" => "Received 50 likes on discussions"
            ];
        }

        // Save the updated badges
        $user->update(['badges' => json_encode($earnedBadges)]);
        return back()->with('success', 'Like status updated.');
    }

    public function report(Request $request, $discussionId)
    {
        $request->validate([
            'reason' => 'required|string|max:500'
        ]);

        $discussion = Discussion::findOrFail($discussionId);

        $reports = json_decode($discussion->reports ?? '[]', true); // Decode existing reports

        // Add new report
        $reports[] = [
            'user_id' => auth()->id(),
            'reason'  => $request->reason,
            'reported_at' => now()->toDateTimeString()
        ];

        // Save updated reports (JSON format)
        $discussion->update(['reports' => json_encode($reports)]);

        return back()->with('success', 'Discussion reported successfully.');
    }

    public function getReportedDiscussions()
    {
        $reportedDiscussions = Discussion::whereNotNull('reports')->get()->map(function ($discussion) {
            $discussion->reports = json_decode($discussion->reports, true);
            return $discussion;
        });

        return view('admin.reported_discussions', compact('reportedDiscussions'));
    }

    public function dismissReports($discussionId)
    {
        $discussion = Discussion::findOrFail($discussionId);
        $discussion->update(['reports' => json_encode([])]); // Clear reports

        return back()->with('success', 'Reports dismissed successfully.');
    }

    public function delete($discussionId)
    {
        $discussion = Discussion::where(function ($query) {
            $query->where('user_id', Auth::id())
                ->orWhereHas('course', function ($q) {
                    $q->where('mentor_id', Auth::id());
                });
        })->findOrFail($discussionId);

        // Delete discussion and its replies
        $discussion->replies()->delete();
        $discussion->delete();

        return back()->with('success', 'Discussion deleted successfully.');
    }
}
