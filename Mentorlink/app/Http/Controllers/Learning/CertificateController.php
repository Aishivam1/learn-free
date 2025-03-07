<?php

namespace App\Http\Controllers\Learning;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class CertificateController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
        $this->middleware('role:mentor')->only('uploadCertificate');
    }

    public function generateCertificate($courseId)
    {
        // Verify course completion and quiz passing
        $course = Course::with('mentor')->findOrFail($courseId);
        
        $enrollment = $course->enrollments()
            ->where('user_id', Auth::id())
            ->where('progress', 100)
            ->firstOrFail();

        $quizAttempt = $course->quizAttempts()
            ->where('user_id', Auth::id())
            ->where('passed', true)
            ->firstOrFail();

        // Check if certificate already exists
        $existingCertificate = Certificate::where('user_id', Auth::id())
            ->where('course_id', $courseId)
            ->first();

        if ($existingCertificate) {
            return response()->json([
                'message' => 'Certificate already generated',
                'certificate' => $existingCertificate
            ]);
        }

        // Generate PDF certificate
        $data = [
            'user_name' => Auth::user()->name,
            'course_name' => $course->title,
            'mentor_name' => $course->mentor->name,
            'completion_date' => now()->format('F d, Y'),
            'certificate_id' => uniqid('CERT-')
        ];

        $pdf = PDF::loadView('certificates.template', $data);
        $filename = 'certificate_' . $courseId . '_' . Auth::id() . '.pdf';
        $path = 'certificates/' . $filename;
        
        Storage::disk('public')->put($path, $pdf->output());

        $certificate = Certificate::create([
            'user_id' => Auth::id(),
            'course_id' => $courseId,
            'certificate_url' => $path,
            'issued_at' => now()
        ]);

        return response()->json([
            'message' => 'Certificate generated successfully',
            'certificate' => $certificate
        ]);
    }

    public function uploadCertificate(Request $request, $courseId)
    {
        $request->validate([
            'certificate' => 'required|file|mimes:pdf|max:5120',
            'user_id' => 'required|exists:users,id'
        ]);

        $course = Course::where('mentor_id', Auth::id())
            ->findOrFail($courseId);

        $path = $request->file('certificate')
            ->store('certificates/manual', 'public');

        $certificate = Certificate::create([
            'user_id' => $request->user_id,
            'course_id' => $courseId,
            'certificate_url' => $path,
            'issued_at' => now(),
            'is_manual' => true
        ]);

        return response()->json([
            'message' => 'Certificate uploaded successfully',
            'certificate' => $certificate
        ]);
    }

    public function download($certificateId)
    {
        $certificate = Certificate::where(function($query) {
                $query->where('user_id', Auth::id())
                    ->orWhereHas('course', function($q) {
                        $q->where('mentor_id', Auth::id());
                    });
            })
            ->findOrFail($certificateId);

        if (!Storage::disk('public')->exists($certificate->certificate_url)) {
            return response()->json([
                'message' => 'Certificate file not found'
            ], 404);
        }

        return Storage::disk('public')->download(
            $certificate->certificate_url,
            'certificate.pdf'
        );
    }

    public function listUserCertificates()
    {
        $certificates = Certificate::with('course')
            ->where('user_id', Auth::id())
            ->latest()
            ->get();

        return response()->json([
            'certificates' => $certificates
        ]);
    }
}
