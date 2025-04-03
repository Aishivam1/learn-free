<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use App\Models\Point;

class Feedback extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'user_id',
        'rating',
        'comment',
        'reported_by'
    ];

    protected $casts = [
        'rating' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'reported_by' => 'array' // Store as JSON array
    ];

    // Relationships
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopePositive($query)
    {
        return $query->where('rating', '>=', 4);
    }

    public function scopeNegative($query)
    {
        return $query->where('rating', '<=', 2);
    }

    // Reporting Feature
    // ✅ Add reportFeedback() method
    public function reportFeedback($userId)
    {
        $reportedUsers = $this->reported_by ?? [];
        if (!in_array($userId, $reportedUsers)) {
            $reportedUsers[] = $userId;
            $this->reported_by = $reportedUsers;
            $this->save();
        }
    }

    // ✅ Check if a user has reported this feedback
    public function isReportedBy($userId)
    {
        return in_array($userId, $this->reported_by ?? []);
    }

    // Helper methods
    public function getRatingText()
    {
        return match ($this->rating) {
            1 => 'Poor',
            2 => 'Fair',
            3 => 'Good',
            4 => 'Very Good',
            5 => 'Excellent',
            default => 'Not Rated'
        };
    }

    public function getStarRating()
    {
        return str_repeat('★', $this->rating) .
            str_repeat('☆', 5 - $this->rating);
    }

    public function canEdit(User $user)
    {
        return $user->id === $this->user_id || $user->isAdmin();
    }

    public function canDelete(User $user)
    {
        return $user->id === $this->user_id ||
            $user->id === $this->course->mentor_id ||
            $user->isAdmin();
    }

    protected static function boot()
    {
        parent::boot();

        static::created(function ($feedback) {
 
            // Award points for feedback
            User::where('id', $feedback->user_id)->increment('points', 10);

            // Update course rating cache
            Cache::tags(['course_ratings'])->forget(
                'course_rating_' . $feedback->course_id
            );
        });

        static::deleted(function ($feedback) {
            // Update course rating cache
            Cache::forget('course_rating_' . $feedback->course_id);

        });
    }
}
