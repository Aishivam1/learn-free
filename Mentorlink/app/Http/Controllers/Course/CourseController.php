<?php

namespace App\Http\Controllers\Course;

use App\Services\GamificationService;
use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Material;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class CourseController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // Show the course creation form (Mentors Only)
    public function create()
    {
        if (Auth::user()->role !== 'mentor') {
            abort(403, 'Unauthorized access.');
        }
        return view('courses.create');
    }

    // Display the list of approved courses
    public function index()
    {
        $user = Auth::user();
        $categories = Course::whereNotNull('category')->distinct()->pluck('category')->toArray();
        $difficulties = Course::whereNotNull('difficulty')->distinct()->pluck('difficulty')->toArray();

        // Get mentors with approved courses
        $mentors = Course::join('users', 'courses.mentor_id', '=', 'users.id')
            ->where('courses.status', 'approved')
            ->select('users.id', 'users.name')
            ->distinct()
            ->pluck('users.name', 'users.id');

        // Ensure mentors see only their approved courses
        $courses = $user->role == 'mentor'
            ? Course::where('mentor_id', $user->id)->where('status', 'approved')->get()
            : Course::where('status', 'approved')->get();

        return view('courses.index', compact('courses', 'mentors', 'categories', 'difficulties'));
    }
    // Display enrolled courses
    public function myCourses()
    {
        $user = Auth::user();
        $categories = Course::whereNotNull('category')->distinct()->pluck('category')->toArray();
        $difficulties = Course::whereNotNull('difficulty')->distinct()->pluck('difficulty')->toArray();

        $mentors = Course::join('users', 'courses.mentor_id', '=', 'users.id')
            ->where('courses.status', 'approved')
            ->select('users.id', 'users.name')
            ->distinct()
            ->pluck('users.name', 'users.id');
        $enrolledCourses = Course::whereHas('enrollments', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })
            ->whereHas('mentor', function ($query) {
                $query->where('role', 'mentor');
            })
            ->with('mentor')
            ->get();

        return view('courses.my', compact('enrolledCourses', 'categories', 'difficulties', 'mentors'));
    }

    // Show a single course with materials
    public function show($id)
    {
        $course = Course::with(['materials', 'feedback.user'])->findOrFail($id);
        $isEnrolled = Auth::check() ? Enrollment::where('user_id', Auth::id())
        ->where('course_id', $id)
        ->exists() : false;
        return view('courses.show', compact('course', 'isEnrolled'));
    }


    // Store a new course (Mentors Only)
    public function store(Request $request)
    {
        if (Auth::user()->role !== 'mentor') {
            abort(403, 'Unauthorized access.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'required|string|max:255',
            'difficulty' => 'required|in:Beginner,Intermediate,Advanced',
            'videos' => 'required|array|min:1', // Ensure at least one video is uploaded
            'videos.*' => 'file|mimes:mp4,mkv,avi,mov|max:51200',
            'pdfs' => 'nullable|array|min:1',
            'pdfs.*' => 'file|mimes:pdf|max:10240'
        ], [
            'videos.required' => 'Please upload at least one video.',
            'videos.min' => 'Please upload at least one video.',
            'videos.*.mimes' => 'Only MP4, MKV, AVI, and MOV videos are allowed.',
            'pdfs.*.mimes' => 'Only PDF files are allowed.',
        ]);


        $course = Course::create([
            'mentor_id' => Auth::id(),
            'title' => $request->title,
            'description' => $request->description,
            'category' => $request->category,
            'difficulty' => $request->difficulty,
            'status' => 'pending'
        ]);

        // Store Videos
        if ($request->hasFile('videos')) {
            foreach ($request->file('videos') as $video) {
                $videoPath = $video->store("courses/{$course->id}/videos", 'public');
                Material::create([
                    'course_id' => $course->id,
                    'type' => 'video',
                    'file_path' => $videoPath,
                    'name' => $video->getClientOriginalName()
                ]);
            }
        }

        // Store PDFs
        if ($request->hasFile('pdfs')) {
            foreach ($request->file('pdfs') as $pdf) {
                $pdfPath = $pdf->store("courses/{$course->id}/pdfs", 'public');
                Material::create([
                    'course_id' => $course->id,
                    'type' => 'pdf',
                    'file_path' => $pdfPath,
                    'name' => $pdf->getClientOriginalName()
                ]);
            }
        }

        return redirect()->route('dashboard')->with('success', 'Course created successfully and pending approval.');
    }

    public function edit($id)
    {
        $course = Course::where('mentor_id', Auth::id())->findOrFail($id);
        return view('courses.edit', compact('course'));
    }

    // Update user progress for a course
    public function updateProgress(Request $request, $courseId)
    {
        $request->validate([
            'progress' => 'required|numeric|min:0|max:100',
            'video_id' => 'required|exists:materials,id'
        ]);

        $userId = Auth::id();

        $enrollment = Enrollment::where('user_id', $userId)
            ->where('course_id', $courseId)
            ->first();

        if (!$enrollment) {
            return redirect()->back()->with('error', 'You are not enrolled in this course.');
        }

        $enrollment->progress = $request->progress;

        if ($request->progress == 100) {
            $enrollment->completed_at = now();
        }

        $enrollment->save();

        return redirect()->back()->with('success', 'Progress updated successfully.');
    }

    // Update course details (Mentors Only)
    public function update(Request $request, $courseId)
    {
        $course = Course::where('mentor_id', Auth::id())->findOrFail($courseId);

       
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'required|string|max:255',
            'difficulty' => 'required|in:Beginner,Intermediate,Advanced',
            'videos' => 'required|array|min:1', // Ensure at least one video is uploaded
            'videos.*' => 'file|mimes:mp4,mkv,avi,mov|max:51200',
            'pdfs' => 'nullable|array|min:1',
            'pdfs.*' => 'file|mimes:pdf|max:10240'
        ], [
            'videos.required' => 'Please upload at least one video.',
            'videos.min' => 'Please upload at least one video.',
            'videos.*.mimes' => 'Only MP4, MKV, AVI, and MOV videos are allowed.',
            'pdfs.*.mimes' => 'Only PDF files are allowed.',
        ]);
        // Initialize a flag to track if any changes were made
        $changes = false;

        // Check if basic course details were updated
        $updateData = $request->only(['title', 'description', 'category', 'difficulty']);
        if (array_filter($updateData)) {
            $course->update($updateData);
            $changes = true;
        }

        // Remove selected materials
        $removedVideos = (array) $request->remove_videos;
        if (!empty($removedVideos)) {
            Material::whereIn('id', $removedVideos)
                ->where('course_id', $courseId)
                ->delete();
            $changes = true;
        }

        $removedPdfs = (array) $request->remove_pdfs;
        if (!empty($removedPdfs)) {
            Material::whereIn('id', $removedPdfs)
                ->where('course_id', $courseId)
                ->delete();
            $changes = true;
        }

        // Handle new video uploads
        if ($request->hasFile('videos')) {
            foreach ($request->file('videos') as $video) {
                $videoPath = $video->store("courses/{$course->id}/videos", 'public');
                Material::create([
                    'course_id' => $course->id,
                    'type' => 'video',
                    'file_path' => $videoPath,
                    'name' => $video->getClientOriginalName()
                ]);
                $changes = true;
            }
        }

        // Handle new PDF uploads
        if ($request->hasFile('pdfs')) {
            foreach ($request->file('pdfs') as $pdf) {
                $pdfPath = $pdf->store("courses/{$course->id}/pdfs", 'public');
                Material::create([
                    'course_id' => $course->id,
                    'type' => 'pdf',
                    'file_path' => $pdfPath,
                    'name' => $pdf->getClientOriginalName()
                ]);
                $changes = true;
            }
        }

        // Check if any changes were made
        if (!$changes) {
            return redirect()->route('courses.index')->with('info', 'No changes were made to the course.');
        }

        return redirect()->route('courses.index')->with('success', 'Course updated successfully.');
    }
    public function rejectedCourses()
    {
        $rejectedCourses = Course::where('status', 'rejected')
            ->where('mentor_id', auth()->id()) // use correct column name
            ->get();

        return view('courses.rejected', compact('rejectedCourses'));
    }


    // Delete a course (Mentors Only)
    public function destroy($id)
    {
        $course = Course::where('mentor_id', Auth::id())->findOrFail($id);

        if ($course->enrollments()->count() > 0) {
            return redirect()->back()->with('error', 'Cannot delete course with active enrollments.');
        }

        foreach ($course->materials as $material) {
            Storage::delete("public/{$material->file_path}");
            $material->delete();
        }

        $course->delete();

        return redirect()->route('courses.index')->with('success', 'Course deleted successfully.');
    }

    public function completeCourse($courseId)
    {
        $user = auth()->user();
        $course = Course::findOrFail($courseId);

        // Check if the user is enrolled in the course
        $enrollment = $user->enrollments()->where('course_id', $courseId)->first();

        if (!$enrollment) {
            return back()->with('error', 'You are not enrolled in this course.');
        }

        // Check if progress is 100%
        if ($enrollment->progress < 100) {
            return back()->with('error', 'You must complete the course to mark it as completed.');
        }

        // Award points only if the user hasn't already completed the course
        if (!$enrollment->completed) {
            $enrollment->update(['completed' => true]); // Mark course as completed
            GamificationService::awardPoints($user, 100);

            // Check total completed courses for badge assignment
            $completedCourses = $user->enrollments()->where('completed', true)->count();
            $badges = json_decode($user->badges, true) ?? [];

            // Assign badges based on the number of completed courses
            if ($completedCourses >= 10 && !array_search("Advanced Learner", array_column($badges, 'name'))) {
                $badges[] = [
                    "id" => 3,
                    "name" => "Advanced Learner",
                    "icon" => "advanced.png",
                    "description" => "Completed 10 courses"
                ];
            } elseif ($completedCourses >= 5 && !array_search("Intermediate Learner", array_column($badges, 'name'))) {
                $badges[] = [
                    "id" => 2,
                    "name" => "Intermediate Learner",
                    "icon" => "intermediate.png",
                    "description" => "Completed 5 courses"
                ];
            } elseif ($completedCourses >= 1 && !array_search("Beginner Learner", array_column($badges, 'name'))) {
                $badges[] = [
                    "id" => 1,
                    "name" => "Beginner Learner",
                    "icon" => "beginner.png",
                    "description" => "Completed 1 course"
                ];
            }

            // Save updated badges
            $user->update(['badges' => json_encode($badges)]);

            return back()->with('success', 'Course completed! You earned 100 points.');
        }

        return back()->with('info', 'You have already completed this course.');
    }
    public function getEnrollmentStatus(Course $course)
    {
        $userId = auth()->id();
        $enrollment = $course->enrollments()->where('user_id', $userId)->first();

        // Also, get quiz attempts count
        $quizAttempts = $course->quizAttempts->where('user_id', $userId)->count();

        return response()->json([
            'progress' => $enrollment ? (int)$enrollment->progress : 0,
            'quizAttempts' => $quizAttempts,
        ]);
    }
}
