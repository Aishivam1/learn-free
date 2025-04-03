<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\QuizSubmission;


class Quiz extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'question',
        'options',
        'correct_answer'
    ];

    protected $casts = [
        'options' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Relationships
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function attempts()
    {
        return $this->hasMany(QuizAttempt::class);
    }

    // Helper methods
    public function getOptions()
    {
        return $this->options;
    }

    public function getOptionsArray()
    {
        return $this->options;
    }

    public function checkAnswer($selectedOption)
    {
        return $selectedOption === $this->correct_answer;
    }

    public function getCorrectAnswer()
    {
        return $this->correct_answer;
    }

    public function quizzes()
    {
        return $this->hasMany(Quiz::class);
    }
    public function getSuccessRate()
    {
        $totalAttempts = $this->attempts()->count();
        if ($totalAttempts === 0) return 0;

        $correctAttempts = $this->attempts()
            ->where('is_correct', true)
            ->count();

        return ($correctAttempts / $totalAttempts) * 100;
    }
    
}
