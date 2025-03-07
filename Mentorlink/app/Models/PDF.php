<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class PDF extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'pdf_url',
        'title',
        'description',
        'file_size',
        'download_count'
    ];

    protected $casts = [
        'file_size' => 'integer',
        'download_count' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Relationships
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function downloads()
    {
        return $this->hasMany(PDFDownload::class);
    }

    // Helper methods
    public function getDownloadUrl()
    {
        return url('storage/' . $this->pdf_url);
    }

    public function incrementDownloadCount()
    {
        $this->increment('download_count');
    }

    public function getFormattedFileSize()
    {
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        $index = 0;

        while ($bytes >= 1024 && $index < count($units) - 1) {
            $bytes /= 1024;
            $index++;
        }

        return round($bytes, 2) . ' ' . $units[$index];
    }

    public function isDownloadableBy(User $user)
    {
        // Check if user is enrolled in the course
        return $user->enrollments()
            ->where('course_id', $this->course_id)
            ->exists() ||
            $user->id === $this->course->mentor_id ||
            $user->isAdmin();
    }

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($pdf) {
            // Delete the actual file when model is deleted
            Storage::disk('public')->delete($pdf->pdf_url);
        });
    }
}
