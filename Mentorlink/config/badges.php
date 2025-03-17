<?php
return [
    // Learning Progress Badges
    [
        'id' => 1,
        'name' => 'Beginner Learner',
        'icon' => 'beginner.png',
        'description' => 'Complete 1 course',
        'points' => 10,
        'criteria' => ['courses_completed' => 1]
    ],
    [
        'id' => 2,
        'name' => 'Intermediate Learner',
        'icon' => 'intermediate.png',
        'description' => 'Complete 5 courses',
        'points' => 20,
        'criteria' => ['courses_completed' => 5]
    ],
    [
        'id' => 3,
        'name' => 'Advanced Learner',
        'icon' => 'advanced.png',
        'description' => 'Complete 10 courses',
        'points' => 30,
        'criteria' => ['courses_completed' => 10]
    ],

    // Quiz Performance Badges
    [
        'id' => 4,
        'name' => 'Sharp Mind',
        'icon' => 'sharp_mind.png',
        'description' => 'Score 90%+ on a quiz',
        'points' => 15,
        'criteria' => ['quiz_score' => 90, 'quiz_attempts' => 1]
    ],
    [
        'id' => 5,
        'name' => 'Quiz Master',
        'icon' => 'quiz_master.png',
        'description' => 'Score 90%+ on 5 quizzes',
        'points' => 25,
        'criteria' => ['quiz_score' => 90, 'quiz_attempts' => 5]
    ],

    // Engagement Badges
    [
        'id' => 6,
        'name' => 'Active Contributor',
        'icon' => 'active_contributor.png',
        'description' => 'Post 10 discussions',
        'points' => 10,
        'criteria' => ['discussions_created' => 10]
    ],
    [
        'id' => 7,
        'name' => 'Community Helper',
        'icon' => 'community_helper.png',
        'description' => 'Receive 50 likes on discussions',
        'points' => 15,
        'criteria' => ['discussion_likes' => 50]
    ],

    // Consistency Badges
    [
        'id' => 8,
        'name' => 'Streak Holder',
        'icon' => 'streak_holder.png',
        'description' => 'Log in daily for 7 days',
        'points' => 20,
        'criteria' => ['login_streak' => 7]
    ],
    [
        'id' => 9,
        'name' => 'Committed Learner',
        'icon' => 'committed_learner.png',
        'description' => 'Use platform for 30 days',
        'points' => 30,
        'criteria' => ['days_active' => 30]
    ],
];
