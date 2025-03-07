<?php

namespace App\Http\Controllers\Learning;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\PDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PDFController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
        $this->middleware('role:mentor')->only('upload');
        $this->middleware('enrolled')->only('download');
    }

    public function upload(Request $request, $courseId)
    {
        $request->validate([
            'pdf_files.*' => 'required|mimes:pdf|max:10240', // 10MB max
            'descriptions.*' => 'nullable|string|max:255'
        ]);

        $course = Course::where('mentor_id', Auth::id())
            ->findOrFail($courseId);

        $uploadedPdfs = [];

        foreach ($request->file('pdf_files') as $index => $file) {
            $path = $file->store("courses/{$courseId}/pdfs", 'public');
            
            $pdf = PDF::create([
                'course_id' => $courseId,
                'file_path' => $path,
                'description' => $request->descriptions[$index] ?? null,
                'file_name' => $file->getClientOriginalName(),
                'file_size' => $file->getSize()
            ]);

            $uploadedPdfs[] = $pdf;
        }

        return response()->json([
            'message' => 'PDFs uploaded successfully',
            'pdfs' => $uploadedPdfs
        ], 201);
    }

    public function download($pdfId)
    {
        $pdf = PDF::findOrFail($pdfId);

        // Verify enrollment
        $enrollment = $pdf->course->enrollments()
            ->where('user_id', Auth::id())
            ->firstOrFail();

        if (!Storage::disk('public')->exists($pdf->file_path)) {
            return response()->json([
                'message' => 'PDF file not found'
            ], 404);
        }

        return Storage::disk('public')->download(
            $pdf->file_path,
            $pdf->file_name
        );
    }

    public function listCoursePDFs($courseId)
    {
        $pdfs = PDF::where('course_id', $courseId)
            ->select('id', 'description', 'file_name', 'file_size', 'created_at')
            ->get();

        return response()->json([
            'pdfs' => $pdfs
        ]);
    }

    public function delete($pdfId)
    {
        $pdf = PDF::whereHas('course', function($query) {
                $query->where('mentor_id', Auth::id());
            })
            ->findOrFail($pdfId);

        // Delete file from storage
        Storage::disk('public')->delete($pdf->file_path);
        
        $pdf->delete();

        return response()->json([
            'message' => 'PDF deleted successfully'
        ]);
    }
}
