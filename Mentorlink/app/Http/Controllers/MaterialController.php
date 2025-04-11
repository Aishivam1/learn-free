<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Material;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Response;

class MaterialController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth'); // Ensure only logged-in users can access this controller
    }

    // ✅ Serve Video Files
    public function streamVideo($id)
    {
        $material = Material::where('id', $id)->where('type', 'video')->firstOrFail();

        if (!Storage::exists('public/' . $material->file_path)) {
            abort(404, 'Video not found.');
        }

        return response()->file(Storage::path('public/' . $material->file_path), [
            'Content-Type' => 'video/mp4'
        ]);
    }

    // ✅ Serve PDFs
    public function viewPdf($id)
    {
        $material = Material::where('id', $id)->where('type', 'pdf')->firstOrFail();

        if (!Storage::exists('public/' . $material->file_path)) {
            abort(404, 'PDF not found.');
        }

        return response()->file(Storage::path('public/' . $material->file_path), [
            'Content-Type' => 'application/pdf'
        ]);
    }
    public function updateProgress(Request $request, Material $material)
    {
        $validated = $request->validate([
            'course_id' => 'required|exists:courses,id',
            'progress' => 'required|integer|min:0|max:100',
        ]);

        $userId = auth()->id();
        $progressData = $material->progress ?? [];

        // Update only if the new progress is greater than the previous one
        if (!isset($progressData[$userId]) || $validated['progress'] > $progressData[$userId]) {
            $progressData[$userId] = $validated['progress'];
            $material->progress = $progressData;
            $material->save();
        }

        // Always recalculate overall enrollment progress
        $this->checkCourseCompletion($validated['course_id']);

        return response()->json([
            'success' => true,
            'progress' => $validated['progress'],
            'material_id' => $material->id
        ]);
    }

    /**
     * Check if all materials in a course are completed and update enrollment progress
     *
     * @param  int  $courseId
     * @return void
     */
    private function checkCourseCompletion($courseId)
    {
        $course = Course::findOrFail($courseId);
        $userId = auth()->id();

        // Filter only video materials
        $videos = $course->materials->where('type', 'video');
        $totalVideos = $videos->count();

        // Sum the progress for all videos for this user
        $totalProgress = 0;
        foreach ($videos as $video) {
            // If progress is not set for a video, treat it as 0
            $progress = isset($video->progress[$userId]) ? $video->progress[$userId] : 0;
            $totalProgress += $progress;
        }

        // Calculate average progress
        $overallProgress = $totalVideos > 0 ? round($totalProgress / $totalVideos) : 0;

        // Update enrollment progress
        Enrollment::where('user_id', $userId)
            ->where('course_id', $courseId)
            ->update(['progress' => $overallProgress]);
    }
    public function destroy($id)
    {
        $material = Material::find($id);
        
        if (!$material) {
            return response()->json(['message' => 'Material not found'], 404);
        }

        if ($material->course->mentor_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $material->delete();
        
        return response()->json(['message' => 'Material deleted successfully'], 200);
    }
}
