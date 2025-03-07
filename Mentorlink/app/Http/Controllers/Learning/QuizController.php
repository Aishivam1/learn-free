<?php

namespace App\Http\Controllers\Learning;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QuizController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:sanctum', 'enrolled']);
    }

    public function show($courseId)
    {
        $course = Course::findOrFail($courseId);
        
        // Check if user has completed the course
        $enrollment = $course->enrollments()
            ->where('user_id', Auth::id())
            ->where('progress', 100)
            ->firstOrFail();

        $quizzes = $course->quizzes()
            ->select('id', 'question', 'options')
            ->get();

        return response()->json([
            'quizzes' => $quizzes
        ]);
    }

    public function submit(Request $request, $courseId)
    {
        $request->validate([
            'answers' => 'required|array',
            'answers.*' => 'required|string'
        ]);

        $course = Course::findOrFail($courseId);
        $quizzes = $course->quizzes;
        
        $correctAnswers = 0;
        $totalQuestions = $quizzes->count();

        foreach ($quizzes as $index => $quiz) {
            if (isset($request->answers[$index]) && 
                $request->answers[$index] === $quiz->correct_answer) {
                $correctAnswers++;
            }
        }

        $score = ($correctAnswers / $totalQuestions) * 100;
        $passed = $score >= 70; // 70% passing threshold

        $attempt = QuizAttempt::create([
            'user_id' => Auth::id(),
            'course_id' => $courseId,
            'score' => $score,
            'passed' => $passed
        ]);

        if ($passed) {
            // Award points and generate certificate
            event(new QuizPassed($attempt));
        }

        return response()->json([
            'score' => $score,
            'passed' => $passed,
            'correct_answers' => $correctAnswers,
            'total_questions' => $totalQuestions
        ]);
    }

    public function retake($courseId)
    {
        $lastAttempt = QuizAttempt::where('user_id', Auth::id())
            ->where('course_id', $courseId)
            ->latest()
            ->first();

        if (!$lastAttempt || !$lastAttempt->passed) {
            return response()->json([
                'message' => 'Quiz available for retake',
                'quizzes' => $this->show($courseId)->original['quizzes']
            ]);
        }

        return response()->json([
            'message' => 'Already passed this quiz',
            'last_attempt' => $lastAttempt
        ], 400);
    }

    public function getAttempts($courseId)
    {
        $attempts = QuizAttempt::where('user_id', Auth::id())
            ->where('course_id', $courseId)
            ->latest()
            ->get();

        return response()->json([
            'attempts' => $attempts
        ]);
    }
}
