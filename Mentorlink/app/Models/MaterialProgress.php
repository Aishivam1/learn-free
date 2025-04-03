<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaterialProgress extends Model
{
    use HasFactory;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 
        'material_id',
        'course_id',
        'progress'
    ];
    
    /**
     * Get the material that owns the progress.
     */
    public function material()
    {
        return $this->belongsTo(Material::class);
    }
    
    /**
     * Get the user that owns the progress.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    /**
     * Get the course that owns the progress.
     */
    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}