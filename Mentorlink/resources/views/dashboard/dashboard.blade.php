@extends('layouts.app')

@section('title', 'Dashboard - MentorLink')

@section('content')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        console.log("Chart Data:", {!! json_encode($usersGrowthData ?? []) !!});
    </script>
    @if (Auth::user()->role == 'learner')
        <!-- 3D Cube Animation -->
        <div class="cube">
            <div class="face front"></div>
            <div class="face back"></div>
            <div class="face right"></div>
            <div class="face left"></div>
            <div class="face top"></div>
            <div class="face bottom"></div>
        </div>
    @endif
    @if (Auth::user()->role == 'mentor')
        <!-- 3D Books Animation Background -->
        <div class="books-animation">
            <div class="book book1"></div>
            <div class="book book2"></div>
            <div class="book book3"></div>
        </div>
    @endif
    @if (Auth::user()->role == 'admin')
        <div class="admin-3d-animation ">
            <div class="gear gear1"></div>
        </div>
    @endif

    @if (Auth::user()->role == 'learner')
        <section class="dashboard">
            <h2>Dashboard</h2>
            <p>Welcome back, {{ Auth::user()->name }}! Here's an overview of your learning progress.</p>

            <!-- Stats Section -->
            <div class="stats">
                <div class="stat">
                    <h3>Points</h3>
                    <p>{{ Auth::user()->points }}</p>
                </div>
                <div class="stat">
                    <h3>Badges</h3>
                    <p>{{ is_array(Auth::user()->badges) ? count(Auth::user()->badges) : count(json_decode(Auth::user()->badges, true) ?? []) }}
                    </p>
                </div>
                <div class="stat">
                    <h3>Courses</h3>
                    <p>{{ count($enrolled_courses) }} Enrolled</p>
                </div>
            </div>

            <!-- Enrolled Courses Section -->
            <div class="courses">
                <h3>Enrolled Courses</h3>
                @forelse($enrolled_courses as $course)
                    <div class="course">
                        <h4>{{ $course->title }}</h4>
                        <p>Instructor: {{ $course->instructor }}</p>
                        <div class="progress">
                            <div class="progress-bar" style="width: {{ $course->progress }}%;">
                                {{ $course->progress }}%
                            </div>
                        </div>
                        <p>Progress: {{ $course->progress }}%</p>
                    </div>
                @empty
                    <p>You are not enrolled in any courses yet.</p>
                @endforelse
            </div>

            <!-- Badges Section -->
            <div class="badges">
                <h3>Earned Badges</h3>
                @php
                    $earnedBadges = is_array(Auth::user()->badges)
                        ? Auth::user()->badges
                        : json_decode(Auth::user()->badges, true) ?? [];
                @endphp


                @if (count($earnedBadges) > 0)
                    <div class="badge-list">
                        @foreach ($earnedBadges as $badge)
                            <div class="badge">
                                <img src="{{ asset('badges/' . $badge['icon']) }}" width="50" height="50"
                                    alt="{{ $badge['name'] }}">
                                <p>{{ $badge['name'] }}</p>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p>You haven't earned any badges yet.</p>
                @endif

                <a href="{{ route('badges.index') }}" class="btn btn-primary mt-3">View All Badges</a>
            </div>
        </section>
    @endif

    @if (Auth::user()->role == 'mentor')
        <section class="dashboard">
            <h2>Mentor Dashboard</h2>
            <p>Welcome back, {{ Auth::user()->name }}! Here's an overview of your courses and student performance.</p>

            <div class="stats">
                <div class="stat">
                    <h3>Enrollments</h3>
                    <p>{{ $mentor['enrollments'] }}</p>
                </div>
                <div class="stat">
                    <h3>Active Students</h3>
                    <p>{{ $mentor['activeStudents'] ?? 0 }}</p>
                </div>
                <div class="stat">
                    <h3>Courses</h3>
                    <p>{{ $mentor['coursesCount'] ?? 0 }}</p>
                </div>
            </div>

            <div class="courses">
                <h3>Course Performance</h3>
                <div class="course-list">
                    @forelse($mentor['coursePerformances'] ?? [] as $course)
                        <div class="course">
                            <h4>{{ $course['title'] }}</h4>
                            <p>Enrollments: {{ $course['enrollments'] }}</p>
                            <div class="progress">
                                <div class="progress-bar" style="width: {{ $course['averageProgress'] }}%;">
                                    {{ $course['averageProgress'] }}% Average Progress
                                </div>
                            </div>
                        </div>
                    @empty
                        <p>No course performance data available.</p>
                    @endforelse
                </div>
            </div>

            <div class="reviews">
                <h3>Pending Reviews</h3>
                @forelse($mentor['reviews'] ?? [] as $review)
                    <div class="review">
                        <h4>{{ $review['courseTitle'] }}</h4>
                        <p>Submitted on: {{ $review['submittedOn'] }}</p>
                        <p class="status">Status: {{ $review['status'] }}</p>
                    </div>
                @empty
                    <p>No pending reviews.</p>
                @endforelse
            </div>
        </section>
    @endif
    @if (Auth::user()->role === 'admin')
        <div class="container">
            <h2>Admin Dashboard</h2>
            <div class="overview-cards">
                <div class="overview-card">
                    <h3>Total Users</h3>
                    <p>{{ $totalUsers }}</p>
                </div>
                <div class="overview-card">
                    <h3>Total Courses</h3>
                    <p>{{ $totalCourses ?? 0 }}</p>
                </div>
                <div class="overview-card">
                    <h3>Enrollments</h3>
                    <p>{{ $totalEnrollments ?? 0 }}</p>
                </div>
                <div class="overview-card">
                    <h3>Quiz Pass Rate</h3>
                    <p>{{ $quizPassRate ?? 0 }}%</p>
                </div>
            </div>

            <!-- Charts Section -->
            <div class="charts">
                <div class="chart-container">
                    <h3>Users Growth Chart</h3>
                    <canvas id="usersGrowthChart"></canvas>
                </div>
                <div class="chart-container">
                    <h3>Course Completion Rate</h3>
                    <canvas id="courseCompletionChart"></canvas>
                </div>
            </div>

            <div class="chart-container">
                <h3>Quiz Success Trends</h3>
                <canvas id="quizSuccessChart"></canvas>
            </div>

        </div>
    @endif
@endsection

@push('styles')
    <style>
        .dashboard {
            padding: 50px 20px;
            text-align: center;
        }

        .dashboard h2 {
            font-size: 36px;
            color: #333;
            margin-bottom: 20px;
        }

        .dashboard p {
            font-size: 18px;
            color: #666;
            margin-bottom: 40px;
        }

        .dashboard .stats {
            display: flex;
            justify-content: space-around;
            margin-bottom: 40px;
        }

        .dashboard .stats .stat {
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            width: 200px;
            text-align: center;
        }

        .dashboard .stats .stat h3 {
            font-size: 24px;
            color: #007bff;
            margin-bottom: 10px;
        }

        .dashboard .stats .stat p {
            font-size: 18px;
            color: #666;
            margin-top: 10px;
        }

        .dashboard .courses {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
        }

        .dashboard .courses h3 {
            font-size: 24px;
            color: #333;
            margin-bottom: 20px;
        }

        .dashboard .courses .course {
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            text-align: left;
        }

        .dashboard .courses .course h4 {
            font-size: 20px;
            color: #007bff;
            margin-bottom: 10px;
        }

        .dashboard .courses .course p {
            font-size: 16px;
            color: #666;
            margin-bottom: 10px;
        }

        .dashboard .courses .course .progress {
            background-color: #f1f1f1;
            border-radius: 5px;
            overflow: hidden;
            margin-bottom: 10px;
        }

        .dashboard .courses .course .progress .progress-bar {
            background-color: #007bff;
            height: 20px;
            width: 70%;
            text-align: center;
            color: #fff;
            line-height: 20px;
        }

        .dashboard .reviews {
            margin-bottom: 40px;
        }

        .dashboard .reviews h3 {
            font-size: 24px;
            color: #333;
            margin-bottom: 20px;
        }

        .dashboard .reviews .review {
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            text-align: left;
        }

        .dashboard .reviews .review h4 {
            font-size: 20px;
            color: #007bff;
            margin-bottom: 10px;
        }

        .dashboard .reviews .review p {
            font-size: 16px;
            color: #666;
            margin-bottom: 10px;
        }

        .dashboard .reviews .review .status {
            font-size: 16px;
            color: #007bff;
        }

        @media (max-width: 768px) {
            header {
                flex-direction: column;
                padding: 20px;
            }

            .dashboard .stats {
                flex-direction: column;
                align-items: center;
            }

            .dashboard .stats .stat {
                width: 100%;
                margin-bottom: 20px;
            }
        }

        /* 3D Books Floating Animation */
        .books-animation {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: -2;
        }

        .book {
            position: absolute;
            width: 60px;
            height: 80px;
            background: url('{{ asset('images/book.gif') }}') no-repeat center center;
            background-size: contain;
            animation: floatBook 10s infinite ease-in-out;
            opacity: 0.7;
        }

        .book.book1 {
            top: 10%;
            left: 15%;
            animation-duration: 8s;
        }

        .book.book2 {
            top: 50%;
            left: 80%;
            animation-duration: 12s;
        }

        .book.book3 {
            top: 80%;
            left: 40%;
            animation-duration: 10s;
        }

        @keyframes floatBook {
            0% {
                transform: translateY(0) rotate(0deg);
            }

            50% {
                transform: translateY(-20px) rotate(10deg);
            }

            100% {
                transform: translateY(0) rotate(0deg);
            }
        }

        /* Smooth Fade-in Animation for Dashboard */
        .dashboard {
            opacity: 0;
            transform: translateY(20px);
            animation: fadeIn 1s ease-in-out forwards;
        }

        @keyframes fadeIn {
            0% {
                opacity: 0;
                transform: translateY(20px);
            }

            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Floating Effect on Stats & Cards */
        .stat,
        .course,
        .review {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .stat:hover,
        .course:hover,
        .review:hover {
            transform: translateY(-5px);
            box-shadow: 0px 5px 15px rgba(0, 0, 0, 0.2);
        }

        .admin-3d-animation {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: -2;
        }

        .gear {
            position: absolute;
            width: 200px;
            height: 100px;
            background: url('{{ asset('images/gear1.gif') }}') no-repeat center center;
            background-size: contain;
            opacity: 1;
            animation: rotateGear 20s infinite linear;
            repeat: not(1);
        }

        .gear1 {
            top: 15%;
            right:25px;
            animation-duration: 18s;
        }

        body {
            font-family: 'Roboto', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f7fa;
            color: #333;
        }

        .container {
            padding: 40px;
            text-align: center;
        }

        .container h2 {
            margin-bottom: 20px;
            color: #007bff;
        }

        .overview-cards {
            display: flex;
            justify-content: space-around;
            margin-bottom: 40px;
        }

        .overview-card {
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            width: 200px;
            text-align: center;
        }

        .overview-card h3 {
            margin-bottom: 10px;
            color: #333;
        }

        .overview-card p {
            font-size: 24px;
            color: #007bff;
        }

        .charts {
            display: flex;
            justify-content: space-around;
            margin-bottom: 40px;
        }

        .chart-container {
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            width: 45%;
        }

        .recent-activity {
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            text-align: left;
        }

        .recent-activity h3 {
            margin-bottom: 20px;
            color: #333;
        }

        .recent-activity ul {
            list-style-type: none;
            padding: 0;
        }

        .recent-activity ul li {
            background-color: #f1f1f1;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 10px;
        }

        /* Global Styles */
        body {
            font-family: 'Roboto', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f7fa;
            color: #333;
        }

        /* Dashboard Section */
        .dashboard {
            padding: 50px 20px;
            text-align: center;
        }

        .dashboard h2 {
            font-size: 36px;
            color: #333;
            margin-bottom: 20px;
        }

        .dashboard p {
            font-size: 18px;
            color: #666;
            margin-bottom: 40px;
        }

        .stats {
            display: flex;
            justify-content: space-around;
            margin-bottom: 40px;
        }

        .stat {
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            width: 200px;
            text-align: center;
        }

        .stat h3 {
            font-size: 24px;
            color: #007bff;
            margin-bottom: 10px;
        }

        .stat p {
            font-size: 18px;
            color: #666;
        }

        /* Courses */
        .courses {
            margin-bottom: 40px;
        }

        .courses h3 {
            font-size: 24px;
            color: #333;
            margin-bottom: 20px;
        }

        .course {
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            text-align: left;
        }

        .course h4 {
            font-size: 20px;
            color: #007bff;
            margin-bottom: 10px;
        }

        .course p {
            font-size: 16px;
            color: #666;
            margin-bottom: 10px;
        }

        .progress {
            background-color: #f1f1f1;
            border-radius: 5px;
            overflow: hidden;
            margin-bottom: 10px;
        }

        .progress-bar {
            background-color: #007bff;
            height: 20px;
            text-align: center;
            color: #fff;
            line-height: 20px;
        }

        /* Badges */
        .badges {
            margin-bottom: 40px;
        }

        .badges h3 {
            font-size: 24px;
            color: #333;
            margin-bottom: 20px;
        }

        .badge {
            display: inline-block;
            background-color: #fff;
            padding: 10px 20px;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin: 10px;
            font-size: 16px;
            color: #007bff;
        }

        /* 3D Cube Animation */
        .cube {
            width: 60px;
            height: 60px;
            position: absolute;
            top: 10%;
            right: 10%;
            transform-style: preserve-3d;
            animation: rotateCube 10s infinite linear;
            z-index: -1;
        }

        .cube .face {
            position: absolute;
            width: 60px;
            height: 60px;
            background: rgba(0, 123, 255, 0.7);
            border: 1px solid #fff;
        }

        .cube .front {
            transform: translateZ(30px);
        }

        .cube .back {
            transform: rotateY(180deg) translateZ(30px);
        }

        .cube .right {
            transform: rotateY(90deg) translateZ(30px);
        }

        .cube .left {
            transform: rotateY(-90deg) translateZ(30px);
        }

        .cube .top {
            transform: rotateX(90deg) translateZ(30px);
        }

        .cube .bottom {
            transform: rotateX(-90deg) translateZ(30px);
        }

        @keyframes rotateCube {
            from {
                transform: rotateX(0deg) rotateY(0deg);
            }

            to {
                transform: rotateX(360deg) rotateY(360deg);
            }
        }

        /* Responsive */
        @media (max-width: 768px) {
            header {
                flex-direction: column;
                padding: 20px;
            }

            .stats {
                flex-direction: column;
                align-items: center;
            }

            .stat {
                width: 100%;
                margin-bottom: 20px;
            }
        }
    </style>
@endpush

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.11.4/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.11.4/ScrollTrigger.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            console.log("Admin Dashboard Loaded");

            // Debugging: Check if Chart.js is available
            if (typeof Chart === "undefined") {
                console.error("Chart.js is not loaded!");
                return;
            }

            // Debugging: Log Data
            console.log("Users Growth Labels:", {!! json_encode($usersGrowthLabels ?? []) !!});
            console.log("Users Growth Data:", {!! json_encode($usersGrowthData ?? []) !!});
            console.log("Course Completion Labels:", {!! json_encode($courseCompletionLabels ?? []) !!});
            console.log("Course Completion Data:", {!! json_encode($courseCompletionData ?? []) !!});
            console.log("Quiz Success Labels:", {!! json_encode($quizSuccessLabels ?? []) !!});
            console.log("Quiz Success Data:", {!! json_encode($quizSuccessData ?? []) !!});

            // Users Growth Chart
            const usersGrowthCanvas = document.getElementById('usersGrowthChart');
            if (usersGrowthCanvas) {
                const usersGrowthCtx = usersGrowthCanvas.getContext('2d');
                new Chart(usersGrowthCtx, {
                    type: 'line',
                    data: {
                        labels: {!! json_encode($usersGrowthLabels ?? []) !!},
                        datasets: [{
                            label: 'Users Growth',
                            data: {!! json_encode($usersGrowthData ?? []) !!},
                            backgroundColor: 'rgba(0, 123, 255, 0.2)',
                            borderColor: 'rgba(0, 123, 255, 1)',
                            borderWidth: 1
                        }]
                    }
                });
            } else {
                console.error("usersGrowthChart element not found!");
            }

            // Course Completion Chart
            const courseCompletionCanvas = document.getElementById('courseCompletionChart');
            if (courseCompletionCanvas) {
                const courseCompletionCtx = courseCompletionCanvas.getContext('2d');
                new Chart(courseCompletionCtx, {
                    type: 'pie',
                    data: {
                        labels: {!! json_encode($courseCompletionLabels ?? []) !!},
                        datasets: [{
                            data: {!! json_encode($courseCompletionData ?? []) !!},
                            backgroundColor: ['#dc3545', '#ffc107', '#198754']
                        }]
                    }
                });
            } else {
                console.error("courseCompletionChart element not found!");
            }

            // Quiz Success Chart
            const quizSuccessCanvas = document.getElementById('quizSuccessChart');
            if (quizSuccessCanvas) {
                const quizSuccessCtx = quizSuccessCanvas.getContext('2d');
                new Chart(quizSuccessCtx, {
                    type: 'bar',
                    data: {
                        labels: {!! json_encode($quizSuccessLabels ?? []) !!},
                        datasets: [{
                            label: 'Quiz Success Rate',
                            data: {!! json_encode($quizSuccessData ?? []) !!},
                            backgroundColor: 'rgba(0, 123, 255, 0.2)',
                            borderColor: 'rgba(0, 123, 255, 1)',
                            borderWidth: 1
                        }]
                    }
                });
            } else {
                console.error("quizSuccessChart element not found!");
            }

            // GSAP Animations
            gsap.registerPlugin(ScrollTrigger);

            // Animation for stats cards
            gsap.utils.toArray('.stat, .overview-card').forEach(stat => {
                gsap.from(stat, {
                    opacity: 0,
                    y: 50,
                    scale: 0.8,
                    duration: 1,
                    scrollTrigger: {
                        trigger: stat,
                        start: 'top 80%',
                        toggleActions: 'play none none reverse'
                    }
                });
            });

            // Animation for courses and reviews
            gsap.utils.toArray('.course, .review').forEach(item => {
                gsap.from(item, {
                    opacity: 0,
                    x: -50,
                    duration: 1,
                    scrollTrigger: {
                        trigger: item,
                        start: 'top 80%',
                        toggleActions: 'play none none reverse'
                    }
                });
            });

            // Chart animations
            gsap.utils.toArray('.chart-container').forEach(chart => {
                gsap.from(chart, {
                    opacity: 0,
                    scale: 0.7,
                    duration: 1,
                    scrollTrigger: {
                        trigger: chart,
                        start: 'top 80%',
                        toggleActions: 'play none none reverse'
                    }
                });
            });

            // Badges animation
            gsap.utils.toArray('.badge').forEach((badge, index) => {
                gsap.from(badge, {
                    opacity: 0,
                    y: 50,
                    rotation: -20,
                    duration: 0.5,
                    delay: index * 0.1,
                    scrollTrigger: {
                        trigger: badge,
                        start: 'top 80%',
                        toggleActions: 'play none none reverse'
                    }
                });
            });

            // Header animation
            gsap.from('header', {
                opacity: 1,
                y: 0,
                duration: 1
            });

            // Dashboard title animation
            gsap.from('p', {
                opacity: 0,
                scale: 0.5,
                duration: 1,
                ease: 'back.out(1.7)'
            });

            // Floating animation for cubes, books, and gears
            gsap.utils.toArray('.cube .face, .book, .gear').forEach(element => {
                gsap.to(element, {
                    y: '+=20',
                    rotation: '+=10',
                    duration: 2,
                    repeat: -1,
                    yoyo: true,
                    ease: 'power1.inOut'
                });
            });
        });
    </script>
@endpush
