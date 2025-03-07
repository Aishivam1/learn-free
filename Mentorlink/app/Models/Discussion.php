<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Discussion extends Model
{
    use HasFactory ;

    protected $fillable = [
        'course_id',
        'user_id',
        'content'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    protected $with = ['user']; // Always load the user relationship

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function parent()
    {
        return $this->belongsTo(Discussion::class, 'parent_id');
    }

    public function replies()
    {
        return $this->hasMany(Discussion::class, 'parent_id')
            ->orderBy('created_at', 'asc');
    }

    public function reports()
    {
        return $this->morphMany(Report::class, 'reportable');
    }

    // Scopes
    public function scopeParentOnly($query)
    {
        return $query->whereNull('parent_id');
    }

    public function scopeRecent($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    // Helper methods
    public function isParent()
    {
        return is_null($this->parent_id);
    }

    public function hasReplies()
    {
        return $this->replies()->exists();
    }

    public function getReplyCount()
    {
        return $this->replies()->count();
    }

    public function canEdit(User $user)
    {
        return $user->id === $this->user_id ||
               $user->id === $this->course->mentor_id ||
               $user->isAdmin();
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

        static::created(function ($discussion) {
            if ($discussion->parent_id) {
                // Notify parent discussion author
                $discussion->parent->user->notify(
                    new NewDiscussionReply($discussion)
                );
            } else {
                // Notify course mentor
                $discussion->course->mentor->notify(
                    new NewDiscussion($discussion)
                );
            }

            // Award points for participation
            event(new DiscussionCreated($discussion));
        });
    }
}
