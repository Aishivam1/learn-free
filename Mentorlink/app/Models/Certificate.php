<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Certificate extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'course_id',
        'certificate_url',
        'issued_at'
    ];

    protected $casts = [
        'issued_at' => 'datetime'
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}
