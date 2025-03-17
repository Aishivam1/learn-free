<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'mentor_id',
        'title',
        'description',
        'category', // ✅ Ensure category is fillable
        'difficulty',
        'status', // pending, approved
        'video_required' // if true, videos must be watched in sequence
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'video_required' => 'boolean'
    ];

    // Relationships
    public function mentor()
    {
        return $this->belongsTo(User::class, 'mentor_id');
    }

    public function quizAttempts()
    {
        return $this->hasMany(QuizAttempt::class);
    }

    // Access instructor name dynamically
    public function getInstructorAttribute()
    {
        return $this->mentor ? $this->mentor->name : 'Unknown Mentor';
    }


    public function materials()
    {
        return $this->hasMany(Material::class);
    }

    public function videos()
    {
        return $this->hasMany(Material::class)->where('type', 'video');
    }

    public function pdfs()
    {
        return $this->hasMany(Material::class)->where('type', 'pdf');
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    public function learners()
    {
        return $this->belongsToMany(User::class, 'enrollments')
            ->withPivot('progress', 'completed_at');
    }

    public function quizzes()
    {
        return $this->hasMany(Quiz::class);
    }

    public function discussions()
    {
        return $this->hasMany(Discussion::class);
    }
    public function feedback()
    {
        return $this->hasMany(Feedback::class);
    }

    public function certificates()
    {
        return $this->hasMany(Certificate::class);
    }

    // Scopes
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    // Helper Methods
    public function isApproved()
    {
        return $this->status === 'approved';
    }

    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function getAverageRating()
    {
        return $this->feedback()->avg('rating') ?? 0;
    }

    public function getCompletionRate()
    {
        $totalEnrollments = $this->enrollments()->count();
        if ($totalEnrollments === 0) return 0;

        $completedEnrollments = $this->enrollments()
            ->whereNotNull('completed_at')
            ->count();

        return ($completedEnrollments / $totalEnrollments) * 100;
    }

    public function getEnrollmentCount()
    {
        return $this->learners()->count();
    }
}
