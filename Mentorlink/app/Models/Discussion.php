<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Discussion extends Model
{
    use HasFactory;

    protected $fillable = ['course_id', 'user_id', 'message','parent_id', 'reports'];
    protected $casts = [
        'reports' => 'array', // Automatically handle JSON conversion
    ];
    
    public function parent()
    {
        return $this->belongsTo(Discussion::class, 'parent_id');
    }

    // Replies to this discussion
    public function replies()
    {
        return $this->hasMany(Discussion::class, 'parent_id')->orderBy('created_at', 'asc');
    }

    // User who created the discussion
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}
