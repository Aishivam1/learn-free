<?php

namespace App\Http\Controllers\Learning;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\Course;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class CertificateController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index()
    {
        $user = Auth::user(); // Get logged-in user
        $certificates = Certificate::where('user_id', $user->id)->get(); // Fetch user's certificates

        return view('certificates.index', compact('certificates'));
    }

    public function generate($courseId)
    {
        $course = Course::with('mentor')->findOrFail($courseId);

        // Verify user eligibility
        $enrollment = $course->enrollments()
            ->where('user_id', Auth::id())
            ->where('progress', 100)
            ->firstOrFail();

        $quizPassed = $course->quizAttempts()
            ->where('user_id', Auth::id())
            ->where('passed', true)
            ->exists();

        if (!$quizPassed) {
            return redirect()->back()->withErrors('You must pass the quiz to receive a certificate.');
        }

        // Check if certificate already exists
        $certificate = Certificate::where('user_id', Auth::id())
            ->where('course_id', $courseId)
            ->first();
        $mentor_name = $course->mentor->name;

        if (!$certificate) {
            // Prepare certificate data
            $data = [
                'user_name' => Auth::user()->name,
                'course_name' => $course->title,
                'mentor_name' => $course->mentor->name,
                'completion_date' => now()->format('F d, Y'),
                'certificate_id' => uniqid('CERT-')
            ];
            $mentor_name = $course->mentor->name;
            $pdf = PDF::loadView('certificates.template', $data);
            $filename = 'certificate_' . $courseId . '_' . Auth::id() . '.pdf';
            $path = 'certificates/' . $filename;

            Storage::disk('public')->put($path, $pdf->output());

            // Save certificate record
            $certificate = Certificate::create([
                'user_id' => Auth::id(),
                'course_id' => $courseId,
                'certificate_url' => $path,
                'issued_at' => now()
            ]);
        }

        // Show the certificate in a view
        return view('certificates.view', compact('certificate', 'course','mentor_name'));
    }

    public function download($courseId)
    {
        $certificate = Certificate::where('user_id', Auth::id())
            ->where('course_id', $courseId)
            ->firstOrFail();

        if (!Storage::disk('public')->exists($certificate->certificate_url)) {
            return response()->json(['message' => 'Certificate file not found'], 404);
        }

        return Storage::disk('public')->download(
            $certificate->certificate_url,
            'Certificate_' . $certificate->id . '.pdf'
        );
    }
}
