<?php
namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BadgeService
{
    protected $badges = [
        ['id' => 1, 'name' => 'Beginner Learner', 'icon' => 'beginner.png', 'description' => 'Completed 1 course'],
        ['id' => 2, 'name' => 'Intermediate Learner', 'icon' => 'intermediate.png', 'description' => 'Completed 5 courses'],
        ['id' => 3, 'name' => 'Advanced Learner', 'icon' => 'advanced.png', 'description' => 'Completed 10 courses'],
        ['id' => 4, 'name' => 'Sharp Mind', 'icon' => 'sharp_mind.png', 'description' => 'Scored 90%+ on a quiz'],
        ['id' => 5, 'name' => 'Quiz Master', 'icon' => 'quiz_master.png', 'description' => 'Scored 90%+ on 5 quizzes'],
        ['id' => 6, 'name' => 'Active Contributor', 'icon' => 'active_contributor.png', 'description' => 'Posted 10 discussions'],
        ['id' => 7, 'name' => 'Community Helper', 'icon' => 'community_helper.png', 'description' => 'Received 50 likes on discussions'],
        ['id' => 8, 'name' => 'Streak Holder', 'icon' => 'streak_holder.png', 'description' => 'Logged in daily for 7 days'],
        ['id' => 9, 'name' => 'Committed Learner', 'icon' => 'committed_learner.png', 'description' => 'Used platform for 30 days'],
    ];

    public function checkAndAwardBadges(User $user)
    {
        $earnedBadges = json_decode($user->badges, true) ?? [];

        // **1. Learning Progress Badges**
        $completedCourses = DB::table('course_completions')->where('user_id', $user->id)->count();
        if ($completedCourses >= 1) $this->awardBadge($user, 1, $earnedBadges);
        if ($completedCourses >= 5) $this->awardBadge($user, 2, $earnedBadges);
        if ($completedCourses >= 10) $this->awardBadge($user, 3, $earnedBadges);

        // **2. Quiz Performance Badges**
        $highScoreQuizzes = DB::table('quiz_attempts')
            ->where('user_id', $user->id)
            ->where('score', '>=', 90)
            ->count();
        if ($highScoreQuizzes >= 1) $this->awardBadge($user, 4, $earnedBadges);
        if ($highScoreQuizzes >= 5) $this->awardBadge($user, 5, $earnedBadges);

        // **3. Engagement Badges**
        $discussionsPosted = DB::table('discussions')->where('user_id', $user->id)->count();
        if ($discussionsPosted >= 10) $this->awardBadge($user, 6, $earnedBadges);

        $totalLikes = DB::table('discussion_likes')->where('user_id', $user->id)->count();
        if ($totalLikes >= 50) $this->awardBadge($user, 7, $earnedBadges);

        // **4. Consistency Badges**
        $streakDays = $this->calculateLoginStreak($user);
        if ($streakDays >= 7) $this->awardBadge($user, 8, $earnedBadges);

        $totalDaysUsed = DB::table('user_logins')->where('user_id', $user->id)->distinct()->count('date');
        if ($totalDaysUsed >= 30) $this->awardBadge($user, 9, $earnedBadges);
    }

    private function awardBadge(User $user, $badgeId, &$earnedBadges)
    {
        if (!collect($earnedBadges)->firstWhere('id', $badgeId)) {
            $badge = collect($this->badges)->firstWhere('id', $badgeId);
            if ($badge) {
                $earnedBadges[] = $badge;
                $user->badges = json_encode($earnedBadges);
                $user->save();
            }
        }
    }

    private function calculateLoginStreak(User $user)
    {
        $dates = DB::table('user_logins')->where('user_id', $user->id)->pluck('date')->toArray();
        sort($dates);

        $streak = 0;
        $previousDate = null;

        foreach ($dates as $date) {
            if ($previousDate && Carbon::parse($previousDate)->diffInDays($date) == 1) {
                $streak++;
            } else {
                $streak = 1;
            }
            $previousDate = $date;
        }

        return $streak;
    }
}
