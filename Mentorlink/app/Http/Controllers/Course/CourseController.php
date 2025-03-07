<?php

namespace App\Http\Controllers\Course;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CourseController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function create()
    {
        if (Auth::user()->role !== 'mentor') {
            abort(403, 'Unauthorized action.');
        }
        return view('courses.create');
    }


    // Return the approved courses listing as an HTML view
    public function index()
    {
        $user = Auth::user();

        // Fetch unique categories & difficulties
        $categories = Course::whereNotNull('category')->distinct()->pluck('category')->toArray();
        $difficulties = Course::whereNotNull('difficulty')->distinct()->pluck('difficulty')->toArray();

        // Get only mentors who have approved courses
        $mentors = Course::join('users', 'courses.mentor_id', '=', 'users.id')
            ->where('courses.status', 'approved')
            ->select('users.id', 'users.name')
            ->distinct()
            ->pluck('users.name', 'users.id');

        // Fetch courses based on role
        if ($user->role == 'mentor') {
            $courses = Course::where('mentor_id', $user->id)->get();
        } else {
            $courses = Course::where('status', 'approved')->get();
        }

        return view('courses.index', compact('courses', 'mentors', 'categories', 'difficulties'));
    }


    public function myCourses()
    {
        $user = Auth::user();

        $enrolledCourses = Course::whereHas('enrollments', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })
            ->whereHas('mentor', function ($query) {
                $query->where('role', 'mentor'); // Ensure mentor exists
            })
            ->with('mentor')
            ->get();

        return view('courses.my', compact('enrolledCourses'));
    }
    // Return the details of a specific course as an HTML view

    public function show($id)
    {
        $course = Course::with('materials')->findOrFail($id);

        return view('courses.show', compact('course'));
    }

    public function store(Request $request)
    {
        if (Auth::user()->role !== 'mentor') {
            abort(403, 'Unauthorized access.');
        }
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'video_url' => 'required|url',
            'pdf_files.*' => 'nullable|mimes:pdf|max:10240'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $course = new Course();
        $course->mentor_id = Auth::id();
        $course->title = $request->title;
        $course->description = $request->description;
        $course->video_url = $request->video_url;
        $course->status = 'pending';
        $course->save();

        if ($request->hasFile('pdf_files')) {
            foreach ($request->file('pdf_files') as $pdf) {
                $path = $pdf->store('course_pdfs/' . $course->id, 'public');
                $course->pdfs()->create(['file_path' => $path]);
            }
        }

        return response()->json([
            'message' => 'Course created successfully and pending approval',
            'course' => $course
        ], 201);
    }

    public function update(Request $request, $courseId)
    {
        $course = Course::where('mentor_id', Auth::id())
            ->findOrFail($courseId);

        $validator = Validator::make($request->all(), [
            'title' => 'string|max:255',
            'description' => 'string',
            'video_url' => 'url'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $course->update($request->only([
            'title',
            'description',
            'video_url'
        ]));

        return response()->json([
            'message' => 'Course updated successfully',
            'course' => $course
        ]);
    }

    public function destroy($courseId)
    {
        $course = Course::where('mentor_id', Auth::id())
            ->findOrFail($courseId);

        if ($course->enrollments()->count() > 0) {
            return response()->json([
                'message' => 'Cannot delete course with active enrollments'
            ], 403);
        }

        $course->delete();

        return response()->json([
            'message' => 'Course deleted successfully'
        ]);
    }
}
