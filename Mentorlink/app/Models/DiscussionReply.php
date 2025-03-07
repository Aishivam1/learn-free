<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DiscussionReply extends Model
{
    use HasFactory;

    protected $fillable = [
        'discussion_id',
        'user_id',
        'content',
        'parent_id',
        'is_solution',
        'likes_count'
    ];

    protected $casts = [
        'is_solution' => 'boolean',
        'likes_count' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Relationships
    public function discussion()
    {
        return $this->belongsTo(Discussion::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function parent()
    {
        return $this->belongsTo(DiscussionReply::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(DiscussionReply::class, 'parent_id');
    }

    public function likes()
    {
        return $this->morphMany(Like::class, 'likeable');
    }

    public function reports()
    {
        return $this->morphMany(Report::class, 'reportable');
    }

    // Scopes
    public function scopeSolutions($query)
    {
        return $query->where('is_solution', true);
    }

    public function scopeTopLevel($query)
    {
        return $query->whereNull('parent_id');
    }

    public function scopeMostLiked($query)
    {
        return $query->orderByDesc('likes_count');
    }

    // Helper methods
    public function markAsSolution()
    {
        if ($this->is_solution) return;

        $this->update(['is_solution' => true]);

        // Award points to the reply author
        Point::award($this->user_id, 'solution_marked');

        // Notify the reply author
        $this->user->notify(new ReplyMarkedAsSolution($this));
    }

    public function like(User $user)
    {
        if ($this->likes()->where('user_id', $user->id)->exists()) {
            return false;
        }

        $this->likes()->create(['user_id' => $user->id]);
        $this->increment('likes_count');

        // Award points to the reply author
        if ($this->user_id !== $user->id) {
            Point::award($this->user_id, 'reply_liked');
        }

        return true;
    }

    public function unlike(User $user)
    {
        $like = $this->likes()->where('user_id', $user->id)->first();
        if (!$like) return false;

        $like->delete();
        $this->decrement('likes_count');

        return true;
    }

    public function canEdit(User $user)
    {
        return $user->id === $this->user_id || $user->isAdmin();
    }

    public function canDelete(User $user)
    {
        return $user->id === $this->user_id ||
               $user->id === $this->discussion->user_id ||
               $user->isAdmin();
    }

    protected static function boot()
    {
        parent::boot();

        static::created(function ($reply) {
            // Award points for creating a reply
            Point::award($reply->user_id, 'reply_created');

            // Notify discussion author and mentioned users
            if ($reply->user_id !== $reply->discussion->user_id) {
                $reply->discussion->user->notify(
                    new NewDiscussionReply($reply)
                );
            }

            // Notify parent reply author if this is a nested reply
            if ($reply->parent_id &&
                $reply->user_id !== $reply->parent->user_id) {
                $reply->parent->user->notify(
                    new NewDiscussionReply($reply)
                );
            }
        });

        static::deleted(function ($reply) {
            // Remove solution mark if needed
            if ($reply->is_solution) {
                $reply->discussion->update(['has_solution' => false]);
            }
        });
    }
}
