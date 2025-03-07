<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Enrollment extends Model
{
    protected $fillable = [
        'user_id',
        'course_id',
        'progress',
        'completed_at'
    ];

    protected $casts = [
        'completed_at' => 'datetime',
        'progress' => 'integer'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
    public function markCompleted()
    {
        $this->update([
            'progress' => 100,
            'completed_at' => now(),
        ]);
    }
}
