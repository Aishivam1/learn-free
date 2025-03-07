<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'user_id',
        'rating',
        'comment'
    ];

    protected $casts = [
        'rating' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
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

    public function reports()
    {
        return $this->morphMany(Report::class, 'reportable');
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

    // Helper methods
    public function getRatingText()
    {
        return match($this->rating) {
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
            // Notify course mentor
            $feedback->course->mentor->notify(
                new NewFeedbackReceived($feedback)
            );

            // Award points for feedback
            Point::award($feedback->user_id, 'feedback_submitted');

            // Update course rating cache
            Cache::tags(['course_ratings'])->forget(
                'course_rating_' . $feedback->course_id
            );
        });

        static::deleted(function ($feedback) {
            // Update course rating cache
            Cache::tags(['course_ratings'])->forget(
                'course_rating_' . $feedback->course_id
            );
        });
    }
}
