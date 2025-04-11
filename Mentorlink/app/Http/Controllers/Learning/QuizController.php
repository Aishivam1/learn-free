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
use Illuminate\Support\Facades\Log;

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
    public function edit($course_id, $quiz_id = null)
    {
        try {
            // Log the request
            Log::info('Quiz edit initiated for course', [
                'course_id' => $course_id,
                'quiz_id' => $quiz_id,
                'user' => Auth::user()->login,
                'timestamp' => now()
            ]);

            // Find the course
            $course = Course::findOrFail($course_id);
            Log::info('Course found', ['course' => $course->toArray()]);

            // Ensure only the mentor can edit quizzes
            if ($course->mentor_id !== Auth::id()) {
                Log::warning('Unauthorized quiz access attempt', [
                    'course_id' => $course_id,
                    'user_id' => Auth::id(),
                    'mentor_id' => $course->mentor_id
                ]);
                return redirect()->back()
                    ->with('error', 'You are not allowed to edit quizzes for this course.');
            }

            // Fetch all quiz questions for this course
            $relatedQuizzes = Quiz::where('course_id', $course->id)->get();
            Log::info('Related quizzes found', ['count' => $relatedQuizzes->count()]);

            // Decode the options for each related quiz
            foreach ($relatedQuizzes as $relatedQuiz) {
                $relatedQuiz->options = json_decode($relatedQuiz->options, true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    throw new \Exception('Invalid JSON format in quiz options: ' . json_last_error_msg());
                }
            }

            // Get the specified quiz or the first quiz to display initially
            $quiz = $quiz_id ? Quiz::findOrFail($quiz_id) : $relatedQuizzes->first();

            return view('quizzes.edit', compact('quiz', 'course', 'relatedQuizzes'));
        } catch (\Exception $e) {
            Log::error('Error in quiz edit:', [
                'course_id' => $course_id,
                'quiz_id' => $quiz_id,
                'user_id' => Auth::id(),
                'error_message' => $e->getMessage()
            ]);

            return redirect()->back()
                ->with('error', 'Error loading quiz editor: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        try {
            // Validate request
            $validated = $request->validate([
                'question' => 'required|string|max:255',
                'options' => 'required|array|min:2|max:4',
                'options.*' => 'required|string|max:255|distinct',
                'correct_answer' => 'required|string|max:255',
                'course_id' => 'required|exists:courses,id'
            ]);

            // Find the quiz and course
            $quiz = Quiz::findOrFail($id);
            $course = Course::findOrFail($request->course_id);

            // Log the mentor ID and authenticated user ID for debugging
            Log::info('Checking authorization', [
                'mentor_id' => $course->mentor_id,
                'auth_id' => Auth::id()
            ]);

            // Authorization check
            if ($course->mentor_id !== Auth::id()) {
                Log::warning('Unauthorized update attempt', [
                    'course_id' => $course->id,
                    'quiz_id' => $id,
                    'mentor_id' => $course->mentor_id,
                    'auth_id' => Auth::id()
                ]);
                return redirect()->back()
                    ->with('error', 'You are not authorized to update this quiz.');
            }

            // Verify correct_answer is in options array
            if (!in_array($request->correct_answer, $request->options)) {
                return redirect()->back()
                    ->with('error', 'The correct answer must be one of the options.')
                    ->withInput();
            }

            // Update quiz
            $quiz->update([
                'question' => $request->question,
                'options' => json_encode($request->options),
                'correct_answer' => $request->correct_answer
            ]);

            Log::info('Quiz updated successfully', [
                'quiz_id' => $id,
                'user_id' => Auth::id()
            ]);

            return redirect()->route('quiz.edit', ['course_id' => $course->id, 'quiz_id' => $quiz->id])
                ->with('success', 'Quiz question updated successfully.');
        } catch (\Exception $e) {
            Log::error('Error updating quiz:', [
                'quiz_id' => $id,
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return redirect()->back()
                ->with('error', 'Error updating quiz question.')
                ->withInput();
        }
    }
    public function destroy($id)
    {
        try {
            // Find the quiz and course
            $quiz = Quiz::findOrFail($id);
            $course = Course::findOrFail($quiz->course_id);

            // Authorization check
            if ($course->mentor_id !== Auth::id()) {
                return redirect()->back()
                    ->with('error', 'You are not authorized to delete this quiz.');
            }

            // Check if this is the last quiz
            $quizCount = Quiz::where('course_id', $course->id)->count();
            if ($quizCount <= 1) {
                return redirect()->back()
                    ->with('error', 'Cannot delete the last quiz question. A course must have at least one quiz question.');
            }

            // Delete the quiz
            $quiz->delete();

            Log::info('Quiz deleted successfully', [
                'quiz_id' => $id,
                'user_id' => Auth::id()
            ]);

            return redirect()->back()
                ->with('success', 'Quiz question deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Error deleting quiz:', [
                'quiz_id' => $id,
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return redirect()->back()
                ->with('error', 'Error deleting quiz question.');
        }
    }
}
