<?php

namespace App\Http\Controllers\Learning;

use App\Services\GamificationService;
use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Quiz;
use Eloquent;
use App\Services\BadgeService;
use App\Models\Enrollment;
use App\Models\QuizAttempt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QuizController extends Controller
{
    protected $badgeService;
    public function __construct(BadgeService $badgeService)
    {
        $this->middleware('auth');
        $this->badgeService = $badgeService;
    }
    public function create($courseId)
    {
        $course = Course::findOrFail($courseId);

        // ✅ Ensure only mentor can create quizzes
        if ($course->mentor_id !== Auth::id()) {
            return redirect()->back()->with('error', 'You are not allowed to create quizzes for this course.');
        }

        return view('quizzes.create', compact('course'));
    }

    public function store(Request $request, $courseId)
    {
        $request->validate([
            'question' => 'required|string',
            'options' => 'required|array|min:2|max:4',
            'options.*' => 'required|string',
            'correct_answer' => 'required|string|in:' . implode(',', $request->options),
        ]);

        $course = Course::findOrFail($courseId);

        // ✅ Ensure only the mentor can add quizzes
        if ($course->mentor_id !== Auth::id()) {
            return redirect()->back()->with('error', 'Unauthorized access.');
        }

        Quiz::create([
            'course_id' => $course->id,
            'question' => $request->question,
            'options' => json_encode($request->options),
            'correct_answer' => $request->correct_answer,
        ]);

        return redirect()->route('courses.quiz.create', $course->id)->with('success', 'Quiz added successfully.');
    }

    public function show($courseId)
    {
        $course = Course::findOrFail($courseId);
    
        // Ensure the user has completed the course
        $enrollment = $course->enrollments()
            ->where('user_id', Auth::id())
            ->where('progress', 100)
            ->firstOrFail();
    
        // Check quiz attempt limit (max 3 attempts)
        $attemptCount = QuizAttempt::where('user_id', Auth::id())
            ->where('course_id', $courseId)
            ->count();
    
        if ($attemptCount >= 3) {
            return redirect()->route('courses.show', $courseId)
                ->with('error', 'You have reached the maximum attempt limit (3 times).');
        }
    
        // ✅ Get all quiz questions for the course from the `quizzes` table
        $quizzes = Quiz::where('course_id', $courseId)->get();
    
        return view('quizzes.show', compact('course', 'quizzes', 'attemptCount'));
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
        $totalQuestions = count($quizzes);
        $userAnswers = [];

        foreach ($quizzes as $quiz) {
            $userAnswers[$quiz->id] = $request->answers[$quiz->id] ?? null;

            if (isset($request->answers[$quiz->id]) && $request->answers[$quiz->id] === $quiz->correct_answer) {
                $correctAnswers++;
            }
        }

        $score = ($totalQuestions > 0) ? ($correctAnswers / $totalQuestions) * 100 : 0;
        $passed = $score >= 70; // ✅ Passing threshold: 70%

        // ✅ If user already attempted 3 times, remove the oldest attempt
        $attempts = QuizAttempt::where('user_id', Auth::id())
            ->where('course_id', $courseId)
            ->orderBy('attempted_at', 'asc')
            ->get();

        if ($attempts->count() >= 3) {
            $attempts->first()->delete(); // Remove oldest attempt
        }

        // ✅ Save the new attempt
        QuizAttempt::create([
            'user_id' => Auth::id(),
            'course_id' => $courseId,
            'score' => $score,
            'passed' => $passed,
            'attempted_at' => now(),
            'answers' => json_encode($userAnswers) // ✅ Store user answers
        ]);
        $user = Auth::user();

        // ✅ Award badges based on quiz performance
        $earnedBadges = json_decode($user->badges, true) ?? [];
        $badges = $earnedBadges;


        $quizPassedCount = QuizAttempt::where('user_id', $user->id)
            ->where('passed', true)
            ->count();

        // Quiz performance badges
        if ($score >= 90 && !array_search("Quiz Master", array_column($badges, 'name'))) {
            $badges[] = [
                "id" => 5,
                "name" => "Quiz Master",
                "icon" => "quiz_master.png",
                "description" => "Scored 90%+ on 5 quizzes"
            ];
        } elseif ($score >= 90 && !array_search("Sharp Mind", array_column($badges, 'name'))) {
            $badges[] = [
                "id" => 4,
                "name" => "Sharp Mind",
                "icon" => "sharp_mind.png",
                "description" => "Score 90%+ on a quiz"
            ];
        }

        // Save updated badges
        $user->update(['badges' => json_encode($badges)]);

        return redirect()->route('quiz.result', $courseId)
            ->with([
                'score' => $score,
                'passed' => $passed,
                'correct_answers' => $correctAnswers,
                'total_questions' => $totalQuestions
            ]);
    }

    public function attempt($courseId)
    {
        $course = Course::findOrFail($courseId);

        // Check enrollment
        $enrollment = $course->enrollments()
            ->where('user_id', Auth::id())
            ->first();

        if (!$enrollment) {
            return redirect()->back()->with('error', 'You are not enrolled in this course.');
        }

        // Restrict quiz attempts to 3
        $attemptCount = QuizAttempt::where('user_id', Auth::id())
            ->where('course_id', $courseId)
            ->count();

        if ($attemptCount >= 3) {
            return redirect()->route('courses.show', $courseId)
                ->with('error', 'You have reached the maximum attempt limit (3 times).');
        }

        if ($enrollment->progress < 100) {
            return redirect()->back()->with('error', 'You must complete the course to attempt the quiz.');
        }

        $quizzes = Quiz::where('course_id', $courseId)->get();
        return view('quizzes.attempt', compact('course', 'quizzes'));
    }

    public function result($courseId)
    {
        $course = Course::findOrFail($courseId);

        $attempt = QuizAttempt::where('user_id', Auth::id())
            ->where('course_id', $courseId)
            ->orderBy('attempted_at', 'desc')
            ->first();

        if (!$attempt) {
            return redirect()->route('courses.show', $courseId)
                ->with('error', 'No quiz attempt found.');
        }

        // ✅ Get all quizzes for the course (fixing the issue)
        $quizzes = Quiz::where('course_id', $courseId)->get();

        if ($quizzes->isEmpty()) {
            return redirect()->route('courses.show', $courseId)
                ->with('error', 'No quizzes found for this course.');
        }

        // Use the first quiz for display, assuming all quizzes have the same passing criteria
        $quiz = $quizzes->first();

        // ✅ Define `passing_score` dynamically if it's not stored in the database
        $passingScore = 75; // Default passing threshold: 70%

        $answers = json_decode($attempt->answers, true) ?? [];
        $score = $attempt->score ?? 0;
        $courseCompleted = ($course->enrollments()
            ->where('user_id', Auth::id())
            ->where('progress', 100)
            ->exists());

        return view('quizzes.result', compact('course', 'attempt', 'quiz', 'quizzes', 'answers', 'score', 'courseCompleted', 'passingScore'));
    }


    public function retake($courseId)
    {
        $attemptCount = QuizAttempt::where('user_id', Auth::id())
            ->where('course_id', $courseId)
            ->count();

        if ($attemptCount >= 3) {
            return redirect()->route('quiz.attempts', $courseId)
                ->with('error', 'You have reached the maximum attempt limit (3 times).');
        }

        return redirect()->route('quiz.show', $courseId)
            ->with('message', 'You can retake the quiz.');
    }

    public function getAttempts($courseId)
    {
        $attempts = QuizAttempt::where('user_id', Auth::id())
            ->where('course_id', $courseId)
            ->latest()
            ->get();

        return view('quizzes.attempts', compact('attempts', 'courseId'));
    }
    public function submitQuiz(Request $request, $quizId)
    {
        $user = auth()->user();
        $quiz = Quiz::findOrFail($quizId);

        // Validate quiz answers
        $score = $this->calculateQuizScore($request, $quiz);
        $user->quizAttempts()->create([
            'quiz_id' => $quizId,
            'score' => $score,
        ]);
        if ($score >= 70) { // If user passes (70%+ correct)
            GamificationService::awardPoints($user, 50);
            $this->badgeService->checkAndAwardBadges($user); // ✅ Award badges
            return back()->with('success', 'Quiz passed! You earned 50 points.');
        }

        return back()->with('error', 'Quiz failed. Try again!');
    }
}
