<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuizAttempt extends Model
{
    use HasFactory;

    public $timestamps = false; // ✅ Disable default timestamps

    protected $fillable = [
        'user_id',
        'course_id',
        'score',
        'passed',
        'attempted_at' // ✅ Add this field to allow manual timestamping
    ];

    protected $casts = [
        'score' => 'integer',
        'passed' => 'boolean',
        'attempted_at' => 'datetime', // ✅ Ensure Laravel treats this as a date field
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }

    // Scopes
    public function scopePassed($query)
    {
        return $query->where('passed', true);
    }

    public function scopeFailed($query)
    {
        return $query->where('passed', false);
    }

    // Helper methods
    public function isPassed()
    {
        return $this->passed;
    }

    public function canRetake()
    {
        if ($this->passed) return false;

        $lastAttempt = self::where('user_id', $this->user_id)
            ->where('quiz_id', $this->quiz_id)
            ->latest('attempted_at')
            ->first();

        if (!$lastAttempt) return true;

        return $lastAttempt->attempted_at->addHour()->isPast();
    }

    public function getLetterGrade()
    {
        if ($this->score >= 90) return 'A';
        if ($this->score >= 80) return 'B';
        if ($this->score >= 70) return 'C';
        if ($this->score >= 60) return 'D';
        return 'F';
    }

    public function getAttemptNumber()
    {
        return self::where('user_id', $this->user_id)
            ->where('quiz_id', $this->quiz_id)
            ->where('attempted_at', '<=', $this->attempted_at)
            ->count();
    }

    protected static function boot()
    {
        parent::boot();

        static::created(function ($attempt) {
            if ($attempt->status) {
                event(new QuizPassed($attempt));
            }
        });
    }
}
