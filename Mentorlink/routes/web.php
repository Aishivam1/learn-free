<?php

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Course\CourseController;
use App\Http\Controllers\Learning\EnrollmentController;
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

use App\Http\Controllers\MaterialController;
use App\Http\Controllers\Course\CourseApprovalController;
use App\Http\Controllers\Learning\ProgressController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// 🔹 PUBLIC ROUTES
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/about', [HomeController::class, 'about'])->name('about');
Route::get('/mentors', [HomeController::class, 'mentors'])->name('mentors');

// 🔹 Informational Pages
Route::view('/careers', 'careers')->name('careers');
Route::view('/press', 'press')->name('press');
Route::get('/contact', [HomeController::class, 'contact'])->name('contact');
Route::view('/help-center', 'help-center')->name('help-center');
Route::view('/terms-of-service', 'terms-of-service')->name('terms-of-service');
Route::view('/privacy-policy', 'privacy-policy')->name('privacy-policy');
Route::view('/faq', 'faq')->name('faq');

// 🔹 COURSE PREVIEW ROUTES
Route::get('/courses', [CourseController::class, 'index'])->name('courses.index');
Route::get('/courses/{course}', [CourseController::class, 'show'])->name('courses.show');
Route::get('/courses/{course}/preview', [CourseController::class, 'preview'])->name('courses.preview');

// 🔹 AUTHENTICATION ROUTES
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

// 🔹 PROTECTED ROUTES (Authenticated Users)
Route::middleware(['auth'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile Management
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile/update', [ProfileController::class, 'updateProfile'])->name('profile.update');
    Route::delete('/profile/delete', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/profile/avatar', [ProfileController::class, 'updateAvatar'])->name('profile.avatar');

    // Learning Management
    Route::post('/courses/{course}/progress', [CourseController::class, 'updateProgress'])->name('course.progress');
    Route::post('/mark-course-complete', [ProgressController::class, 'markCourseComplete'])->name('progress.complete');
    Route::get('/my-courses', [CourseController::class, 'myCourses'])->name('courses.my');
    Route::get('/course/create', [CourseController::class, 'create'])->name('courses.create');
    Route::post('/courses/store', [CourseController::class, 'store'])->name('courses.store');
    Route::get('/mentor/courses/rejected', [CourseController::class, 'rejectedCourses'])->name('courses.rejected');
    Route::put('/courses/{course}', [CourseController::class, 'update'])->name('courses.update');
    Route::get('/course/{course}/learn', [CourseController::class, 'learn'])->name('courses.learn');
    Route::post('/courses/{id}/enroll', [EnrollmentController::class, 'enroll'])->name('courses.enroll');
    Route::get('/courses/{id}/edit', [CourseController::class, 'edit'])->name('courses.edit');
    Route::delete('/courses/{id}', [CourseController::class, 'destroy'])->name('courses.destroy');
    Route::delete('/course/{course}/unenroll', [EnrollmentController::class, 'unenroll'])->name('courses.unenroll');
    Route::post('/course/{course}/progress', [CourseController::class, 'updateProgress'])->name('courses.progress');

    // Quiz Management
    Route::get('/courses/{id}/quiz/create', [QuizController::class, 'create'])->name('courses.quiz.create');
    Route::post('/courses/{id}/quiz', [QuizController::class, 'store'])->name('quiz.store');
    Route::get('/course/{course}/quizzes', [QuizController::class, 'index'])->name('quizzes.index');
    Route::get('/course/{course}/quiz/{quiz}', [QuizController::class, 'show'])->name('quizzes.show');
    Route::post('/quiz/{quiz}/attempt', [QuizController::class, 'attempt'])->name('quiz.attempt');
    Route::get('/quiz/{quiz}/results', [QuizController::class, 'results'])->name('quizzes.results');

    // Material Access
    Route::get('/materials/video/{id}', [MaterialController::class, 'streamVideo'])->name('materials.video');
    Route::get('/materials/pdf/{id}', [MaterialController::class, 'viewPdf'])->name('materials.pdf');

    // Discussion System
    Route::get('/discussion/{courseId}', [DiscussionController::class, 'listByCourse'])->name('discussion.index');
    Route::get('/discussion/{courseId}/create', [DiscussionController::class, 'showCreateForm'])->name('discussion.create');
    Route::post('/discussion/{course}/create', [DiscussionController::class, 'create'])->name('discussions.store');
    Route::get('/discussion/{id}', [DiscussionController::class, 'show'])->name('discussion.show');
    Route::post('/discussion/{id}/reply', [DiscussionController::class, 'reply'])->name('discussion.reply');
    Route::post('/discussion/{id}/like', [DiscussionController::class, 'toggleLike'])->name('discussion.like');

    // Leaderboard & Badges
    Route::get('/leaderboard', [LeaderboardController::class, 'index'])->name('leaderboard');
    Route::get('/my-badges', [BadgeController::class, 'userBadges'])->name('badges.index');

    // Certificates
    Route::get('/certificate/generate/{courseId}', [CertificateController::class, 'generate'])->name('certificate.generate');
    Route::get('/certificate/download/{courseId}', [CertificateController::class, 'download'])->name('certificate.download');
    Route::get('/my-certificates', [CertificateController::class, 'index'])->name('certificates.index');

    // Feedback
    Route::post('/course/{course}/feedback', [FeedbackController::class, 'store'])->name('feedback.store');
    Route::get('/course/{course}/feedback', [FeedbackController::class, 'index'])->name('feedback.index');
    Route::put('/feedback/{feedback}', [FeedbackController::class, 'update'])->name('feedback.update');
    Route::delete('/feedback/{feedback}', [FeedbackController::class, 'destroy'])->name('feedback.destroy');

    // Admin Routes
    Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/courses/pending', [CourseApprovalController::class, 'listPendingCourses'])->name('courses.pending');
        Route::put('/courses/{course}/approve', [CourseApprovalController::class, 'approve'])->name('courses.approve');
        Route::put('/courses/{course}/reject', [CourseApprovalController::class, 'reject'])->name('courses.reject');
        Route::get('/analytics', [AdminController::class, 'analytics'])->name('analytics');
        Route::get('/users', [AdminController::class, 'users'])->name('users');
        Route::get('/feedback', [AdminController::class, 'feedback'])->name('feedback');
        Route::post('/feedback/{feedback}/feature', [AdminController::class, 'featureFeedback'])->name('feedback.feature');
        Route::get('/reports', [AdminController::class, 'reports'])->name('reports');
        Route::post('/reports/{report}/resolve', [AdminController::class, 'resolveReport'])->name('reports.resolve');
        Route::get('/settings', [AdminController::class, 'settings'])->name('settings');
    });
});
