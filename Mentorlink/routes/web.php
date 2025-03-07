<?php

use App\Http\Controllers\Learning\EnrollmentController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Course\CourseController;
use App\Http\Controllers\User\ProfileController;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Learning\QuizController;
use App\Http\Controllers\Learning\CertificateController;
use App\Http\Controllers\Discussion\DiscussionController;
use App\Http\Controllers\Gamification\BadgeController;
use App\Http\Controllers\Gamification\LeaderboardController;
use App\Http\Controllers\Feedback\FeedbackController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\HomeController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Public routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/about', [HomeController::class, 'about'])->name('about');
Route::get('/contact', [HomeController::class, 'contact'])->name('contact');
Route::get('/mentors', [HomeController::class, 'mentors'])->name('mentors');

// Course preview routes
Route::get('/courses', [CourseController::class, 'index'])->name('courses.index');
Route::get('/courses/{course}', [CourseController::class, 'show'])->name('courses.show');
Route::get('/courses/{course}/preview', [CourseController::class, 'preview'])->name('courses.preview');

// Authentication routes
Route::middleware('guest')->group(function () {
    Route::get('/register', [AuthController::class, 'registerForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/login', [AuthController::class, 'loginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/forgot-password', [AuthController::class, 'forgotPasswordForm'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword'])->name('password.email');
    Route::get('/reset-password/{token}', [AuthController::class, 'resetPasswordForm'])->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
});

// Protected routes
Route::middleware(['auth'])->group(function () {
    // Auth
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');


    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/avatar', [ProfileController::class, 'updateAvatar'])->name('profile.avatar');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Learning
    Route::get('/my-courses', [CourseController::class, 'myCourses'])->name('courses.my');
    Route::get('/course/{course}/learn', [CourseController::class, 'learn'])->name('courses.learn');
    Route::post('/courses/{id}/enroll', [EnrollmentController::class, 'enroll'])->name('courses.enroll');
    Route::delete('/course/{course}/unenroll', [EnrollmentController::class, 'unenroll'])->name('courses.unenroll');
    Route::post('/course/{course}/progress', [CourseController::class, 'updateProgress'])->name('courses.progress');

    // Quizzes
    Route::get('/course/{course}/quizzes', [QuizController::class, 'index'])->name('quizzes.index');
    Route::get('/course/{course}/quiz/{quiz}', [QuizController::class, 'show'])->name('quizzes.show');
    Route::post('/quiz/{quiz}/attempt', [QuizController::class, 'attempt'])->name('quizzes.attempt');
    Route::get('/quiz/{quiz}/results', [QuizController::class, 'results'])->name('quizzes.results');

    // Discussions
    Route::get('/course/{course}/discussions', [DiscussionController::class, 'index'])->name('discussions.index');
    Route::post('/course/{course}/discussions', [DiscussionController::class, 'store'])->name('discussions.store');
    Route::get('/discussions/{discussion}', [DiscussionController::class, 'show'])->name('discussions.show');
    Route::put('/discussions/{discussion}', [DiscussionController::class, 'update'])->name('discussions.update');
    Route::delete('/discussions/{discussion}', [DiscussionController::class, 'destroy'])->name('discussions.destroy');
    Route::post('/discussions/{discussion}/replies', [DiscussionController::class, 'reply'])->name('discussions.reply');
    Route::post('/discussions/{discussion}/like', [DiscussionController::class, 'like'])->name('discussions.like');
    Route::delete('/discussions/{discussion}/unlike', [DiscussionController::class, 'unlike'])->name('discussions.unlike');
    Route::post('/discussions/replies/{reply}/solution', [DiscussionController::class, 'markSolution'])->name('discussions.solution');

    // Badges & Leaderboard
    Route::get('/badges', [BadgeController::class, 'index'])->name('badges.index');
    Route::get('/my-badges', [BadgeController::class, 'userBadges'])->name('badges.my');
    Route::get('/leaderboard', [LeaderboardController::class, 'index'])->name('leaderboard');
    Route::get('/leaderboard/weekly', [LeaderboardController::class, 'weekly'])->name('leaderboard.weekly');
    Route::get('/leaderboard/monthly', [LeaderboardController::class, 'monthly'])->name('leaderboard.monthly');

    // Certificates
    Route::get('/certificates', [CertificateController::class, 'index'])->name('certificates.index');
    Route::get('/certificates/{certificate}', [CertificateController::class, 'download'])->name('certificates.download');

    // Feedback
    Route::post('/course/{course}/feedback', [FeedbackController::class, 'store'])->name('feedback.store');
    Route::get('/course/{course}/feedback', [FeedbackController::class, 'index'])->name('feedback.index');
    Route::put('/feedback/{feedback}', [FeedbackController::class, 'update'])->name('feedback.update');
    Route::delete('/feedback/{feedback}', [FeedbackController::class, 'destroy'])->name('feedback.destroy');

    // Mentor routes
    Route::middleware(['auth'])->group(function () {
        Route::get('/mentor/courses', [CourseController::class, 'mentorCourses'])->name('mentor.courses');
        Route::get('/mentor/courses/create', [CourseController::class, 'create'])->name('courses.create');
        Route::post('/mentor/courses', [CourseController::class, 'store'])->name('courses.store');
        Route::get('/mentor/courses/{course}/edit', [CourseController::class, 'edit'])->name('courses.edit');
        Route::put('/mentor/courses/{course}', [CourseController::class, 'update'])->name('courses.update');
        Route::delete('/mentor/courses/{course}', [CourseController::class, 'destroy'])->name('courses.destroy');
        Route::post('/mentor/courses/{course}/publish', [CourseController::class, 'publish'])->name('courses.publish');
        Route::post('/mentor/courses/{course}/unpublish', [CourseController::class, 'unpublish'])->name('courses.unpublish');
        Route::get('/mentor/students', [DashboardController::class, 'students'])->name('mentor.students');

        // Mentor Quiz Management
        Route::post('/course/{course}/quizzes', [QuizController::class, 'store'])->name('quizzes.store');
        Route::put('/quizzes/{quiz}', [QuizController::class, 'update'])->name('quizzes.update');
        Route::delete('/quizzes/{quiz}', [QuizController::class, 'destroy'])->name('quizzes.destroy');
    });

    // Admin routes
    Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
        // ✅ Course Approval
        Route::get('/courses', [AdminController::class, 'pendingCourses'])->name('courses.pending');
        Route::get('/analytics', [AdminController::class, 'analytics'])->name('analytics');
        Route::get('/users', [AdminController::class, 'users'])->name('users');
        Route::post('/courses/{course}/approve', [AdminController::class, 'approveCourse'])->name('courses.approve');
        Route::post('/courses/{course}/reject', [AdminController::class, 'rejectCourse'])->name('courses.reject');
        Route::get('/feedback', [AdminController::class, 'feedback'])->name('feedback');
        Route::post('/feedback/{feedback}/feature', [AdminController::class, 'featureFeedback'])->name('feedback.feature');
        Route::get('/reports', [AdminController::class, 'reports'])->name('reports');
        Route::post('/reports/{report}/resolve', [AdminController::class, 'resolveReport'])->name('reports.resolve');
        Route::get('/settings', [AdminController::class, 'settings'])->name('settings');
    });
});
