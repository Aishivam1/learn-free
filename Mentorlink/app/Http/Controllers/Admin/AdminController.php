<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Course;
use App\Models\QuizAttempt;
use App\Models\AdminDashboardAnalytics;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function dashboardMetrics()
    {
        // Get or calculate metrics
        $metrics = Cache::remember('admin_dashboard_metrics', 3600, function () {
            $totalUsers = User::count();
            $totalCourses = Course::count();
            $totalEnrollments = DB::table('enrollments')->count();

            $quizSuccessRate = QuizAttempt::where('passed', true)
                ->count() / QuizAttempt::count() * 100;

            // Store metrics in analytics table
            AdminDashboardAnalytics::create([
                'total_users' => $totalUsers,
                'total_courses' => $totalCourses,
                'total_enrollments' => $totalEnrollments,
                'quiz_success_rate' => $quizSuccessRate
            ]);

            return compact(
                'totalUsers',
                'totalCourses',
                'totalEnrollments',
                'quizSuccessRate'
            );
        });

        // Get additional stats
        $additionalStats = [
            'active_users_today' => $this->getActiveUsersCount('today'),
            'active_users_week' => $this->getActiveUsersCount('week'),
            'active_users_month' => $this->getActiveUsersCount('month'),
            'pending_courses' => Course::where('status', 'pending')->count(),
            'completed_courses' => DB::table('enrollments')
                ->whereNotNull('completed_at')
                ->count()
        ];

        return response()->json([
            'metrics' => $metrics,
            'additional_stats' => $additionalStats
        ]);
    }

    private function getActiveUsersCount($period)
    {
        $date = match ($period) {
            'today' => now()->startOfDay(),
            'week' => now()->startOfWeek(),
            'month' => now()->startOfMonth(),
            default => now()
        };

        return User::where('last_login_at', '>=', $date)->count();
    }

    public function listUsers(Request $request)
    {
        $query = User::query();

        // Apply filters
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                    ->orWhere('email', 'like', "%{$request->search}%");
            });
        }

        // Apply sorting
        $sortField = $request->sort_by ?? 'created_at';
        $sortOrder = $request->sort_order ?? 'desc';
        $query->orderBy($sortField, $sortOrder);

        // Simplified eager loading and counts to avoid relationship issues
        $users = $query->with('enrollments')
            ->paginate(15);

        // Get available roles for filter dropdown
        $roles = User::distinct('role')->pluck('role')->filter();

        // Return Blade view with data
        return view('admin.users.index', compact('users', 'roles'));
    }

    // Add this method to handle user deletion
    public function deleteUser($id)
    {
        $user = User::findOrFail($id);

    

        // Delete the user
        $user->delete();

        return redirect()->route('admin.users')->with('success', 'User deleted successfully.');
    }


    public function listCourses(Request $request)
    {
        $query = Course::query();

        // Apply filters
        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->mentor_id) {
            $query->where('mentor_id', $request->mentor_id);
        }

        if ($request->search) {
            $query->where('title', 'like', "%{$request->search}%");
        }

        // Apply sorting
        $sortField = $request->sort_by ?? 'created_at';
        $sortOrder = $request->sort_order ?? 'desc';
        $query->orderBy($sortField, $sortOrder);

        $courses = $query->with(['mentor', 'enrollments'])
            ->withCount(['quizzes', 'discussions'])
            ->paginate(15);

        return response()->json($courses);
    }

    public function getAnalytics(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date'
        ]);

        $analytics = AdminDashboardAnalytics::whereBetween('created_at', [
            $request->start_date,
            $request->end_date
        ])
            ->get();

        // Calculate trends
        $trends = [
            'user_growth' => $this->calculateGrowth($analytics, 'total_users'),
            'course_growth' => $this->calculateGrowth($analytics, 'total_courses'),
            'enrollment_growth' => $this->calculateGrowth($analytics, 'total_enrollments'),
            'quiz_success_trend' => $this->calculateTrend($analytics, 'quiz_success_rate')
        ];

        return response()->json([
            'analytics' => $analytics,
            'trends' => $trends
        ]);
    }

    private function calculateGrowth($data, $field)
    {
        if ($data->count() < 2) return 0;

        $oldest = $data->first()->$field;
        $newest = $data->last()->$field;

        return $oldest ? (($newest - $oldest) / $oldest) * 100 : 0;
    }

    private function calculateTrend($data, $field)
    {
        return $data->pluck($field)->avg();
    }
}
