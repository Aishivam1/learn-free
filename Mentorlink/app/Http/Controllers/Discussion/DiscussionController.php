<?php

namespace App\Http\Controllers\Discussion;

use App\Services\GamificationService;
use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Discussion;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Services\BadgeService;
use Illuminate\Support\Facades\DB;

class DiscussionController extends Controller
{
    public function __construct()
    {
        $this->middleware(middleware: 'auth');
    }

    public function index(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'You must be logged in to view discussions.');
        }

        $user = Auth::user();
        $query = Discussion::whereNull('parent_id')
            ->with(['user:id,name,avatar', 'course:id,title'])
            ->withCount('replies');

        // Get courses based on user role
        if ($user->isMentor()) {
            // For mentors, only show their created courses in the filter
            $courses = Course::select('id', 'title')
                ->where('mentor_id', $user->id)
                ->get();
        } else {
            // For learners and admins, show all courses
            $courses = Course::select('id', 'title')->get();
        }

        // Filtering
        if ($request->has('course_id')) {
            $query->where('course_id', $request->course_id);
        }
        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        if ($request->has('reported') && $request->reported == 1) {
            $query->whereNotNull('reports');
        }

        // Access control
        if ($user->isMentor()) {
            $mentorCourses = Course::where('mentor_id', $user->id)->pluck('id');
            $query->whereIn('course_id', $mentorCourses);
        }
        // Remove the learner access control restriction
        // Now learners can see all discussions

        // Sorting
        $sort = $request->input('sort', 'latest');
        if ($sort === 'oldest') {
            $query->oldest();
        } else {
            $query->latest();
        }

        $discussions = $query->paginate(15);

        // Get current date/time and user info
        $currentDateTime = now()->setTimezone('UTC')->format('Y-m-d H:i:s');
        $currentUser = $user->name;

        return view('discussions.index', compact(
            'discussions',
            'courses',
            'currentDateTime',
            'currentUser',
            'user' // Pass the user object to the view
        ));
    }
    public function listByCourse($courseId)
    {
        $course = Course::findOrFail($courseId); // Fetch course details

        $discussions = Discussion::where('course_id', $courseId)
            ->whereNull('parent_id')
            ->with(['user:id,name', 'replies.user:id,name'])
            ->latest()
            ->paginate(15);

        // Add this line to fetch all courses for the dropdown
        $courses = Course::select('id', 'title')->get();

        return view('discussions.index', compact('discussions', 'course', 'courses'));
    }
    public function showCreateForm($courseId = null)
    {
        if (!Auth::check() || !Auth::user()->isLearner()) {
            return redirect()->route('discussions.index')->with('error', 'Only learners can create discussions.');
        }

        $user = Auth::user();

        // Get only enrolled courses for the learner
        $enrolledCourseIds = DB::table('enrollments')
            ->where('user_id', $user->id)
            ->pluck('course_id');

        $courses = Course::select('id', 'title')
            ->whereIn('id', $enrolledCourseIds)
            ->get();

        if ($courses->isEmpty()) {
            return redirect()->route('courses.index')->with('error', 'You need to enroll in a course before creating discussions.');
        }

        // If course ID is provided, verify enrollment
        $course = null;
        if ($courseId) {
            if (!$enrolledCourseIds->contains($courseId)) {
                return redirect()->route('discussions.index')->with('error', 'You can only create discussions in courses you are enrolled in.');
            }
            $course = Course::find($courseId);
        } else {
            // Default to first enrolled course
            $course = Course::find($enrolledCourseIds->first());
        }

        return view('discussions.create', compact('courses', 'course'));
    }

    public function store(Request $request)
    {
        // Ensure only learners can create discussions
        if (!Auth::check() || !Auth::user()->isLearner()) {
            return back()->with('error', 'Only learners can create discussions.');
        }

        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'message' => 'required|string|max:1000'
        ]);

        $courseId = $request->course_id;
        $user = Auth::user();

        // Ensure the learner is enrolled in this course
        $isEnrolled = DB::table('enrollments')->where([
            ['user_id', $user->id],
            ['course_id', $courseId]
        ])->exists();

        if (!$isEnrolled) {
            return back()->with('error', 'You can only create discussions in courses you are enrolled in.');
        }

        Discussion::create([
            'course_id' => $courseId,
            'user_id' => $user->id,
            'message' => $request->message
        ]);

        // Award points for participation
        GamificationService::awardPoints($user, 20);

        return redirect()->route('discussions.list', ['courseId' => $courseId])
            ->with('success', 'Discussion created successfully.');
    }

    public function show($id)
    {
        $discussion = Discussion::with(['user', 'replies.user'])->findOrFail($id);
        return view('discussions.show', compact('discussion'));
    }

    public function reply(Request $request, $discussionId)
    {
        if (!Auth::check()) {
            return back()->with('error', 'You must be logged in to reply.');
        }

        // All authenticated users can reply (admins, mentors, learners)
        $request->validate([
            'message' => 'required|string|max:1000'
        ]);

        $parentDiscussion = Discussion::findOrFail($discussionId);

        // Check if the user can access this discussion
        $user = Auth::user();
        if (!$user->isAdmin()) {
            if ($user->isMentor()) {
                $mentorCourses = Course::where('mentor_id', $user->id)->pluck('id');
                if (!$mentorCourses->contains($parentDiscussion->course_id)) {
                    return back()->with('error', 'You cannot reply to discussions in courses you do not mentor.');
                }
            } elseif ($user->isLearner()) {
                $enrolledCourses = DB::table('enrollments')->where('user_id', $user->id)->pluck('course_id');
                if (!$enrolledCourses->contains($parentDiscussion->course_id)) {
                    return back()->with('error', 'You cannot reply to discussions in courses you are not enrolled in.');
                }
            }
        }

        Discussion::create([
            'course_id' => $parentDiscussion->course_id,
            'user_id' => Auth::id(),
            'parent_id' => $discussionId, // Nested reply
            'message' => $request->message
        ]);

        // Award points for replying
        GamificationService::awardPoints(Auth::user(), 10);

        return back()->with('success', 'Reply added successfully.');
    }

    public function like($id)
    {
        $discussion = Discussion::findOrFail($id);
        $userId = auth()->id();

        // Ensure $discussion->reports is always a string before decoding
        $reports = is_string($discussion->reports) ? json_decode($discussion->reports, true) : [];
        if (!is_array($reports)) {
            $reports = [];
        }

        // Ensure "likes" key exists
        if (!isset($reports['likes']) || !is_array($reports['likes'])) {
            $reports['likes'] = [];
        }

        if (in_array($userId, $reports['likes'])) {
            // Unlike if already liked
            $reports['likes'] = array_values(array_diff($reports['likes'], [$userId]));
        } else {
            // Like the discussion
            $reports['likes'][] = $userId;
        }

        // Save updated reports back to the database
        $discussion->update(['reports' => json_encode($reports)]);

        return response()->json([
            'success' => true,
            'likeCount' => count($reports['likes'])
        ]);
    }

    public function getLikesCount($discussionId)
    {
        $discussion = Discussion::find($discussionId);

        if (!$discussion) {
            return response()->json(['error' => 'Discussion not found'], 404);
        }

        $reports = is_string($discussion->reports) ? json_decode($discussion->reports, true) : [];
        if (!is_array($reports)) {
            $reports = [];
        }

        $likeCount = isset($reports['likes']) ? count($reports['likes']) : 0;

        return response()->json(['likes' => $likeCount]);
    }

    public function hasUserLiked($discussionId)
    {
        $userId = auth()->id();
        $discussion = Discussion::find($discussionId);

        if (!$discussion) {
            return response()->json(['error' => 'Discussion not found'], 404);
        }

        $reports = is_string($discussion->reports) ? json_decode($discussion->reports, true) : [];
        if (!is_array($reports)) {
            $reports = [];
        }

        if (!isset($reports['likes']) || !is_array($reports['likes'])) {
            $reports['likes'] = [];
        }

        $liked = in_array($userId, $reports['likes']);

        return response()->json(['liked' => $liked]);
    }

    public function report($id)
    {
        $discussion = Discussion::findOrFail($id);
        $userId = auth()->id();

        // Ensure $discussion->reports is always a string before decoding
        $reports = is_string($discussion->reports) ? json_decode($discussion->reports, true) : [];
        if (!is_array($reports)) {
            $reports = [];
        }

        // Ensure "reported_by" key exists
        if (!isset($reports['reported_by']) || !is_array($reports['reported_by'])) {
            $reports['reported_by'] = [];
        }

        if (in_array($userId, $reports['reported_by'])) {
            // Remove report if already reported
            $reports['reported_by'] = array_values(array_diff($reports['reported_by'], [$userId]));
            $userReported = false;
        } else {
            // Add report
            $reports['reported_by'][] = $userId;
            $userReported = true;
        }

        // Save updated reports back to the database
        $discussion->update(['reports' => json_encode($reports)]);

        return response()->json([
            'success' => true,
            'reportCount' => count($reports['reported_by']),
            'userReported' => $userReported
        ]);
    }
    public function isReportedByUser($id)
    {
        $discussion = Discussion::findOrFail($id);
        $userId = auth()->id();

        // Check if the discussion has any reports
        $reports = is_string($discussion->reports) ? json_decode($discussion->reports, true) : [];
        if (!is_array($reports)) {
            $reports = [];
        }

        // Check if the user has reported this discussion
        $isReported = isset($reports['reported_by']) &&
            is_array($reports['reported_by']) &&
            in_array($userId, $reports['reported_by']);

        return response()->json([
            'isReported' => $isReported
        ]);
    }

    public function getReportedDiscussions()
    {
        // Only admins should access this
        if (!Auth::user()->isAdmin()) {
            return redirect()->route('discussions.index')
                ->with('error', 'You do not have permission to view reported discussions.');
        }

        $reportedDiscussions = Discussion::whereNotNull('reports')
            ->get()
            ->filter(function ($discussion) {
                $reports = json_decode($discussion->reports, true);
                return is_array($reports) &&
                    isset($reports['reported_by']) &&
                    is_array($reports['reported_by']) &&
                    count($reports['reported_by']) > 0;
            })
            ->map(function ($discussion) {
                $discussion->reports = json_decode($discussion->reports, true);
                return $discussion;
            });

        return view('admin.reported-discussions', compact('reportedDiscussions'));
    }

    public function dismissReports($discussionId)
    {
        // Only admins should be able to dismiss reports
        if (!Auth::user()->isAdmin()) {
            return back()->with('error', 'You do not have permission to dismiss reports.');
        }

        $discussion = Discussion::findOrFail($discussionId);

        // Keep likes, remove only reports
        $reports = json_decode($discussion->reports, true) ?: [];
        if (!is_array($reports)) {
            $reports = [];
        }

        // Keep likes if they exist
        $likes = isset($reports['likes']) ? $reports['likes'] : [];

        // Reset reports but keep likes
        $discussion->update(['reports' => json_encode(['likes' => $likes, 'reported_by' => []])]);

        return back()->with('success', 'Reports dismissed successfully.');
    }

    public function myDiscussions()
    {
        // Ensure only learners can access this function
        if (!Auth::check() || !Auth::user()->isLearner()) {
            return back()->with('error', 'Only learners can view their discussions.');
        }

        $discussions = Discussion::where('user_id', Auth::id())
            ->whereNull('parent_id') // Only show main discussions (not replies)
            ->with(['course:id,title']) // Fetch course titles for reference
            ->withCount('replies')
            ->latest()
            ->paginate(15);

        return view('discussions.my_discussions', compact('discussions'));
    }

    public function delete($discussionId)
    {
        $discussion = Discussion::findOrFail($discussionId);
        $user = Auth::user();

        // Allow learners to delete their own discussions
        if ($discussion->user_id === $user->id && $user->isLearner()) {
            $this->deleteRepliesRecursively($discussion);
            $discussion->delete();
            return back()->with('success', 'Your discussion has been deleted.');
        }

        // Allow mentors to delete discussions in their courses
        if ($user->isMentor()) {
            $mentorCourses = Course::where('mentor_id', $user->id)->pluck('id');
            if ($mentorCourses->contains($discussion->course_id)) {
                $this->deleteRepliesRecursively($discussion);
                $discussion->delete();
                return back()->with('success', 'Discussion deleted from your course.');
            }
        }

        // Allow admins to delete reported discussions only
        if ($user->isAdmin()) {
            $reports = json_decode($discussion->reports, true) ?: [];
            $reportedBy = $reports['reported_by'] ?? [];

            if (is_array($reportedBy) && count($reportedBy) > 0) {
                $this->deleteRepliesRecursively($discussion);
                $discussion->delete();
                return back()->with('success', 'Reported discussion deleted successfully.');
            } else {
                return back()->with('error', 'Admins can only delete reported discussions.');
            }
        }

        return back()->with('error', 'You are not authorized to delete this discussion.');
    }

    // Recursive function to delete nested replies
    private function deleteRepliesRecursively($discussion)
    {
        foreach ($discussion->replies as $reply) {
            $this->deleteRepliesRecursively($reply);
            $reply->delete();
        }
    }
}
