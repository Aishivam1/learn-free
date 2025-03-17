<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\DB;


class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    public function hasRole($role)
    {
        return $this->role === $role; // Assuming 'role' is a column in the users table
    }

    protected $fillable = [
        'name',
        'email',
        'password',
        'role', // 'learner', 'mentor', 'admin'
        'points',
        'avatar',
        'bio',
        'badges'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'points' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'badges' => 'array',
    ];

    const ROLE_ADMIN = 'admin';
    const ROLE_MENTOR = 'mentor';
    const ROLE_LEARNER = 'learner';

    // Role-based methods
    public function isAdmin()
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isMentor()
    {
        return $this->role === self::ROLE_MENTOR;
    }

    public function isLearner()
    {
        return $this->role === self::ROLE_LEARNER;
    }

    // Relationships - As Mentor
    public function coursesAsMentor()
    {
        return $this->hasMany(Course::class, 'mentor_id');
    }

    public function mentorEnrollments()
    {
        return $this->hasManyThrough(Enrollment::class, Course::class, 'mentor_id', 'course_id', 'id', 'id');
    }

    // Relationships - As Learner
    public function enrolledCourses()
    {
        return $this->belongsToMany(Course::class, 'enrollments')
            ->withPivot('progress', 'completed_at');
    }

    public function quizAttempts()
    {
        return $this->hasMany(QuizAttempt::class);
    }

    public function certificates()
    {
        return $this->hasMany(Certificate::class);
    }

    public function discussions()
    {
        return $this->hasMany(Discussion::class);
    }

    public function feedback()
    {
        return $this->hasMany(Feedback::class);
    }
    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    public function badges()
    {
        return collect(config('badges')); // Returns all badges from the config file
    }

    // Point Management (Leaderboard Without a Table)
    public function addPoints($points, $type)
    {
        $this->increment('points', $points);
        return $this;
    }

    // Get User Rank Dynamically
    public function getRank()
    {
        return User::where('points', '>', $this->points)->count() + 1;
    }

    // Get Top Users for Leaderboard
    public static function getLeaderboard($limit = 10)
    {
        return self::select('id', 'name', 'email', 'points')
            ->orderByDesc('points')
            ->limit($limit)
            ->get();
    }
    public function awardBadge($badgeId, $name, $icon, $description)
    {
        $badges = $this->badges ?? [];

        // Check if the badge already exists
        $exists = collect($badges)->firstWhere('id', $badgeId);

        if (!$exists) {
            $badges[] = [
                "id" => $badgeId,
                "name" => $name,
                "icon" => $icon,
                "description" => $description
            ];

            $this->update(['badges' => $badges]); // Save the updated badges
        }
    }
    public function availableBadges()
    {
        return collect(config('badges')); // Get from config/badges.php
    }
    // Badge Management
    // public function awardBadge($badge)
    // {
    //     $badges = $this->badges ?? [];

    //     // Check if user already has the badge
    //     foreach ($badges as $b) {
    //         if ($b['id'] == $badge['id']) {
    //             return; // Badge already exists
    //         }
    //     }

    //     // Add new badge
    //     $badges[] = $badge;
    //     $this->badges = $badges;
    //     $this->save();

    //     // Add points for earning a badge (Optional)
    //     $this->addPoints($badge['points'] ?? 0, 'badge');
    // }
    public function getBadges()
    {
        return $this->badges ?? [];
    }
}
