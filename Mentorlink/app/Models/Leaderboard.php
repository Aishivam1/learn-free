<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Leaderboard extends Model
{
    protected $fillable = [
        'user_id',
        'points',
        'rank'
    ];

    protected $casts = [
        'points' => 'integer',
        'rank' => 'integer'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Update Rankings
    public static function updateRankings()
    {
        $rank = 1;
        static::orderBy('points', 'desc')
            ->each(function ($entry) use (&$rank) {
                $entry->update(['rank' => $rank++]);
            });
    }

    // Get Top Learners
    public static function getTopLearners($limit = 10)
    {
        return static::with('user')
            ->orderBy('points', 'desc')
            ->limit($limit)
            ->get();
    }

    // Get User Rank
    public static function getUserRank(User $user)
    {
        return static::where('points', '>', $user->leaderboard->points ?? 0)
            ->count() + 1;
    }
}
