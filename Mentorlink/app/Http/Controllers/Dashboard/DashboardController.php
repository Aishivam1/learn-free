<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\User;
use App\Models\Quiz;
use App\Models\Certificate;
use App\Models\Discussion;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display the unified dashboard for all user types.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            abort(403, 'Unauthorized access');
        }

        $data = [
            'points'            => $user->points ?? 0,
            'completed_quizzes' => $this->getCompletedQuizzes($user),
            'enrolled_courses'  => $this->getEnrolledCourses($user),
            'earned_certificates' => $this->getEarnedCertificates($user),
            'available_courses' => Course::where('status', 'published')->count(),
            'progress'          => $this->calculateLearnerProgress($user),
            'achievements'      => $this->getLearnerAchievements($user),
            'enrollments'       => 0, // ✅ Default value to avoid undefined variable
        ];

        if ($user->role == 'admin') {
            $data = array_merge($data, $this->getAdminData()); // ✅ Ensure this line exists
        } elseif ($user->role == 'mentor') {
            $data['mentor'] = $this->getMentorData($user); // ✅ Ensures $mentor is passed correctly
        }
        return view('dashboard.dashboard', $data);
    }


    /**
     * Get completed quizzes count.
     *
     * @param  \App\Models\User  $user
     * @return int
     */
    private function getCompletedQuizzes($user)
    {
        return DB::table('quiz_attempts')
            ->where('user_id', $user->id)
            ->where('passed', 1)
            ->count();
    }

    /**
     * Get enrolled courses for the user.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function getEnrolledCourses($user)
    {
        return Course::select('courses.*', 'enrollments.progress')
            ->join('enrollments', 'courses.id', '=', 'enrollments.course_id')
            ->where('enrollments.user_id', $user->id)
            ->get();
    }

    /**
     * Get earned certificates count.
     *
     * @param  \App\Models\User  $user
     * @return int
     */
    private function getEarnedCertificates($user)
    {
        return Certificate::where('user_id', $user->id)->count();
    }
    /**
     * Calculate the quiz pass rate for all users.
     *
     * @return float
     */
    private function calculateQuizPassRate()
    {
        // Get the total number of quiz attempts
        $totalAttempts = DB::table('quiz_attempts')->count();

        // Get the number of passed quiz attempts (assuming 'passed' column stores 1 for passed, 0 for failed)
        $passedAttempts = DB::table('quiz_attempts')->where('passed', 1)->count();

        // Prevent division by zero
        if ($totalAttempts === 0) {
            return 0;
        }

        // Calculate the pass rate percentage
        return round(($passedAttempts / $totalAttempts) * 100, 2);
    }
    /**
     * Get user growth data for the last 6 months.
     *
     * @return array
     */
    private function getUsersGrowthData()
    {
        return DB::table('users')
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, COUNT(id) as count')
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('count', 'month')
            ->toArray();
    }
    /**
     * Get course completion data.
     *
     * @return array
     */
    private function getCourseCompletionData()
    {
        $totalEnrollments = DB::table('enrollments')->count();

        if ($totalEnrollments == 0) {
            return [
                'labels' => ['Completed', 'In Progress', 'Not Started'],
                'data'   => [0, 0, 0],
            ];
        }

        $completed = DB::table('enrollments')
            ->where('progress', '>=', 100)
            ->count();

        $inProgress = DB::table('enrollments')
            ->whereBetween('progress', [1, 99])
            ->count();

        $notStarted = DB::table('enrollments')
            ->where('progress', 0)
            ->count();

        return [
            'labels' => ['Completed', 'In Progress', 'Not Started'],
            'data'   => [
                round(($completed / $totalEnrollments) * 100, 2),
                round(($inProgress / $totalEnrollments) * 100, 2),
                round(($notStarted / $totalEnrollments) * 100, 2),
            ],
        ];
    }


    /**
     * Get admin-specific data.
     *
     * @return array
     */
    /**
     * Get data for the Admin Dashboard.
     *
     * @return array

     * Get quiz success trends data.
     *
     * @return array
     */
    private function getQuizSuccessData()
    {
        $quizAttempts = DB::table('quiz_attempts')
            ->selectRaw("DATE(attempted_at) as date, COUNT(*) as total_attempts, SUM(passed) as passed_attempts")
            ->groupBy('date')
            ->orderBy('date', 'ASC')
            ->get();


        $labels = [];
        $data = [];

        foreach ($quizAttempts as $attempt) {
            $labels[] = $attempt->date;
            $successRate = ($attempt->total_attempts > 0) ? round(($attempt->passed_attempts / $attempt->total_attempts) * 100, 2) : 0;
            $data[] = $successRate;
        }

        return [
            'labels' => $labels,
            'data'   => $data,
        ];
    }


    private function getAdminData()
    {
        return [
            'totalUsers'         => User::count(),
            'totalCourses'       => Course::count(),
            'totalEnrollments'   => DB::table('enrollments')->count(),
            'quizPassRate'       => $this->calculateQuizPassRate(),
            'usersGrowthData'    => array_values($this->getUsersGrowthData()),
            'usersGrowthLabels'  => array_keys($this->getUsersGrowthData()),
            'courseCompletionLabels' => $this->getCourseCompletionData()['labels'],
            'courseCompletionData'   => $this->getCourseCompletionData()['data'],
            'quizSuccessLabels'  => $this->getQuizSuccessData()['labels'],
            'quizSuccessData'    => $this->getQuizSuccessData()['data'],
        ];
    }


    /**
     * Get mentor-specific data.
     *
     * @param  \App\Models\User  $user
     * @return array
     */
    private function getMentorData($user)
    {
        return [
            'enrollments' => DB::table('enrollments') // ✅ Fixed query
                ->whereIn('course_id', function ($query) use ($user) {
                    $query->select('id')
                        ->from('courses')
                        ->where('mentor_id', $user->id);
                })
                ->count(),

            'activeStudents' => User::whereHas('enrollments', function ($query) use ($user) {
                $query->whereHas('course', function ($q) use ($user) {
                    $q->where('mentor_id', $user->id);
                });
            })->count(),

            'coursesCount' => Course::where('mentor_id', $user->id)->count(),

            'coursePerformances' => Course::where('mentor_id', $user->id)
                ->withCount('enrollments')
                ->get()
                ->map(function ($course) {
                    return [
                        'title' => $course->title,
                        'enrollments' => $course->enrollments_count,
                        'averageProgress' => DB::table('enrollments')
                            ->where('course_id', $course->id)
                            ->avg('progress') ?? 0, // Get average learner progress
                    ];
                }),

            'reviews' => [] // Add logic if needed
        ];
    }

    /**
     * Get recent administrative activities.
     *
     * @return array
     */
    private function getAdminActivities()
    {
        return []; // Implement activity tracking logic
    }

    /**
     * Calculate learner's overall progress.
     *
     * @param  \App\Models\User  $user
     * @return array
     */
    private function calculateLearnerProgress($user)
    {
        return [
            'courses_progress' => 0, // Implement logic to calculate
            'quizzes_progress' => 0, // Implement logic to calculate
            'overall_progress' => 0, // Implement logic to calculate
        ];
    }

    /**
     * Get learner's achievements.
     *
     * @param  \App\Models\User  $user
     * @return array
     */
    private function getLearnerAchievements($user)
    {
        return []; // Implement logic to fetch achievements
    }
}
