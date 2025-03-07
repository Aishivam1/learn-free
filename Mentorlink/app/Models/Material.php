<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Material extends Model
{
    protected $fillable = [
    'course_id', 'type', 'file_path', 'name'

    ];

    protected $casts = [
        'order' => 'integer',
        'downloadable' => 'boolean'
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    // Helper Methods
    public function isVideo()
    {
        return $this->type === 'video';
    }

    public function isPDF()
    {
        return $this->type === 'pdf';
    }

    public function canDownload(User $user)
    {
        if (!$this->downloadable) return false;
        return $user->isAdmin() ||
               $this->course->mentor_id === $user->id ||
               $this->course->learners()->where('user_id', $user->id)->exists();
    }

    public function getUrl()
    {
        return Storage::url($this->file_path);
    }
}
