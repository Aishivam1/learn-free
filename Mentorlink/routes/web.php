<?php

use App\Http\Controllers\Learning\PaymentController;
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
use Illuminate\Support\Facades\Mail;
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
    // Password Reset Routes// Add this temporary test route

    // Test mail route
    Route::get('/test-mail', function () {
        try {
            Mail::raw('Test email from MentorLink', function ($message) {
                $message->to('sp2481646@gmail.com')
                    ->subject('Test Mail');
            });

            return 'Test email sent successfully!';
        } catch (Exception $e) {
            return 'Error sending mail: ' . $e->getMessage();
        }
    });

    // Password Reset Routes
    Route::get('forgot-password', [AuthController::class, 'showForgotPasswordForm'])
        ->name('password.request');

    Route::post('forgot-password', [AuthController::class, 'sendResetLink'])
        ->name('password.email');

    Route::get('reset-password/{token}', [AuthController::class, 'showResetPasswordForm'])
        ->name('password.reset');

    Route::post('reset-password', [AuthController::class, 'resetPassword'])
        ->name('password.update');
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

    Route::get('/courses/{course}/payment', [PaymentController::class, 'showPayment'])->name('courses.payment');
    Route::post('/payment/callback', [PaymentController::class, 'handleCallback'])->name('payment.callback');
    Route::get('/mentor/courses/rejected', [CourseController::class, 'rejectedCourses'])->name('courses.rejected');
    Route::post('/mark-course-complete', [ProgressController::class, 'markCourseComplete'])->name('progress.complete');
    Route::get('/my-courses', [CourseController::class, 'myCourses'])->name('courses.my');
    Route::get('/course/create', [CourseController::class, 'create'])->name('courses.create');
    Route::post('/courses/store', [CourseController::class, 'store'])->name('courses.store');
    Route::put('/courses/{course}', [CourseController::class, 'update'])->name('courses.update');
    Route::delete('/materials/{id}', [MaterialController::class, 'destroy'])->name('materials.destroy');
    Route::get('/course/{course}/learn', [CourseController::class, 'learn'])->name('courses.learn');
    Route::post('/courses/{id}/enroll', [EnrollmentController::class, 'enroll'])->name('courses.enroll');
    Route::get('/courses/{id}/edit', [CourseController::class, 'edit'])->name('courses.edit');
    Route::delete('/courses/{id}', [CourseController::class, 'destroy'])->name('courses.destroy');
    Route::delete('/course/{course}/unenroll', [EnrollmentController::class, 'unenroll'])->name('courses.unenroll');
    Route::post('/materials/{material}/progress', [MaterialController::class, 'updateProgress'])->name('materials.progress');
    // Quiz Management
    Route::get('/courses/{course}/enrollment-status', [CourseController::class, 'getEnrollmentStatus'])->name('courses.enrollment.status');
    Route::get('/courses/{id}/quiz/create', [QuizController::class, 'create'])->name('courses.quiz.create');
    Route::post('/courses/{id}/quiz', [QuizController::class, 'store'])->name('quiz.store');
    // Route to edit the first quiz question of a course
    Route::get('/courses/{course_id}/quizzes/edit', [QuizController::class, 'edit'])->name('quiz.edit');

    // Route to edit a specific quiz question of a course
    Route::get('/courses/{course_id}/quizzes/edit/{quiz_id}', [QuizController::class, 'edit'])->name('quiz.edit.specific');
    // Route to update a specific quiz question of a course
    Route::post('/quizzes/update/{id}', [QuizController::class, 'update'])->name('quiz.update');
    Route::delete('/quiz/{id}/delete', [QuizController::class, 'destroy'])->name('quiz.destroy');
    Route::get('/course/{course}/quizzes', [QuizController::class, 'index'])->name('quizzes.index');
    Route::get('/course/{course}/quiz/{quiz}', [QuizController::class, 'show'])->name('quizzes.show');
    Route::get('/quiz/{quiz}/attempt', [QuizController::class, 'attempt'])->name('quiz.attempt');
    Route::post('/quiz/{quiz}/submit', [QuizController::class, 'submit'])->name('quiz.submit');
    Route::get('/quiz/{quiz}/results', [QuizController::class, 'result'])->name('quiz.result');

    // Material Access
    Route::get('/materials/video/{id}', [MaterialController::class, 'streamVideo'])->name('materials.video');
    Route::get('/materials/pdf/{id}', [MaterialController::class, 'viewPdf'])->name('materials.pdf');
    // Discussion routes
    Route::middleware(['auth'])->group(function () {
        // View discussions
        Route::get('/discussions', [DiscussionController::class, 'index'])->name('discussions.index');
        Route::get('/discussions/create/{courseId?}', [DiscussionController::class, 'showCreateForm'])->name('discussions.create');
        Route::get('/discussions/{id}/is-reported-by-user', [DiscussionController::class, 'isReportedByUser']);
        Route::get('/discussions/course/{courseId}', [DiscussionController::class, 'listByCourse'])->name('discussions.list');
        Route::get('/discussions/my', [DiscussionController::class, 'myDiscussions'])->name('discussions.my');
        Route::get('/discussions/{id}', [DiscussionController::class, 'show'])->name('discussions.show');

        // Create discussions
        Route::post('/discussions/store', [DiscussionController::class, 'store'])->name('discussions.store');

        // Reply to discussions
        Route::post('/discussions/{discussionId}/reply', [DiscussionController::class, 'reply'])->name('discussions.reply');

        // Like and report
        Route::post('/discussions/{id}/like', [DiscussionController::class, 'like'])->name('discussions.like');
        Route::post('/discussions/{id}/report', [DiscussionController::class, 'report'])->name('discussions.report');

        // AJAX endpoints
        Route::get('/discussions/{id}/like-count', [DiscussionController::class, 'getLikesCount'])->name('discussions.like-count');
        Route::get('/discussions/{id}/has-liked', [DiscussionController::class, 'hasUserLiked'])->name('discussions.has-liked');

        // Delete discussions
        Route::delete('/discussions/{id}', [DiscussionController::class, 'delete'])->name('discussions.delete');

        // Admin routes
        Route::middleware(['admin'])->group(function () {
            Route::get('/admin/reported-discussions', [DiscussionController::class, 'getReportedDiscussions'])->name('discussions.reported');
            Route::post('/admin/discussions/{id}/dismiss-reports', [DiscussionController::class, 'dismissReports'])->name('discussions.dismiss-reports');
        });
    });

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
    Route::get('/feedback/{feedback}', [FeedbackController::class, 'update'])->name('feedback.update');
    Route::delete('/feedback/{feedback}', [FeedbackController::class, 'destroy'])->name('feedback.destroy');

    // Admin Routes
    Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/courses/pending', [CourseApprovalController::class, 'listPendingCourses'])->name('courses.pending');
        Route::put('/courses/{course}/approve', [CourseApprovalController::class, 'approve'])->name('courses.approve');
        Route::put('/courses/{course}/reject', [CourseApprovalController::class, 'reject'])->name('courses.reject');
        Route::get('/analytics', [AdminController::class, 'analytics'])->name('analytics');
        Route::get('/users', [AdminController::class, 'listUsers'])->name('users');
        Route::get('/feedback', [AdminController::class, 'feedback'])->name('feedback');
        Route::post('/feedback/{feedback}/feature', [AdminController::class, 'featureFeedback'])->name('feedback.feature');
        Route::get('/reports', [AdminController::class, 'reports'])->name('reports');
        Route::post('/reports/{report}/resolve', [AdminController::class, 'resolveReport'])->name('reports.resolve');
        Route::get('/settings', [AdminController::class, 'settings'])->name('settings');
    });
    Route::middleware(['auth'])->group(function () {
        Route::get('/admin/users', [AdminController::class, 'listUsers'])->name('admin.users');
        Route::get('/admin/users/{user}', [AdminController::class, 'showUser'])->name('admin.users.show');
        Route::delete('/admin/users/{user}', [AdminController::class, 'deleteUser'])->name('admin.users.delete');
    });
});
