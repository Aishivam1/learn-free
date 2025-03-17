<?php

namespace App\Services;

use App\Models\User;

class GamificationService
{
    /**
     * Award points to a user.
     *
     * @param User $user
     * @param int $points
     */
    public static function awardPoints(User $user, int $points)
    {
        $user->increment('points', $points);
    }
}
