<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'title',
        'video_url',
        'order'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'order' => 'integer'
    ];

    // Relationships
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    // Helper methods
    public function getNextVideo()
    {
        return $this->course->videos()
            ->where('order', '>', $this->order)
            ->orderBy('order')
            ->first();
    }

    public function getPreviousVideo()
    {
        return $this->course->videos()
            ->where('order', '<', $this->order)
            ->orderBy('order', 'desc')
            ->first();
    }

    public function isFirstVideo()
    {
        return !$this->course->videos()
            ->where('order', '<', $this->order)
            ->exists();
    }

    public function isLastVideo()
    {
        return !$this->course->videos()
            ->where('order', '>', $this->order)
            ->exists();
    }

    // Reorder videos
    public static function reorder($courseId, $videoIds)
    {
        foreach ($videoIds as $order => $videoId) {
            self::where('id', $videoId)
                ->where('course_id', $courseId)
                ->update(['order' => $order + 1]);
        }
    }
}
