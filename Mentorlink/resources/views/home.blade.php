@extends('layouts.app')

@section('title', 'MentorLink')

@section('content')
    <style>
        /* Home Page Styles */

        /* Hero Section */
        .hero {
            text-align: center;
            padding: 100px 20px;
            background-color: #f5f7fa;
            width: 100%;
        }

        .hero h1 {
            font-size: 48px;
            color: #007bff;
            margin-bottom: 20px;
        }

        .hero h2 {
            font-size: 36px;
            color: #333;
            margin-bottom: 20px;
        }

        .hero p {
            font-size: 18px;
            color: #666;
            margin-bottom: 40px;
        }

        .hero .buttons a {
            margin: 0 10px;
            padding: 15px 30px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: 500;
        }

        .hero .buttons .explore {
            background-color: #007bff;
            color: #fff;
        }

        .hero .buttons .create {
            background-color: #fff;
            color: #007bff;
            border: 1px solid #007bff;
        }

        /* Stats Section */
        .stats {
            display: flex;
            justify-content: space-around;
            padding: 50px 20px;
            background-color: #fff;
            width: 100%;
        }

        .stats .stat {
            text-align: center;
        }

        .stats .stat h3 {
            font-size: 24px;
            color: #007bff;
            margin-bottom: 10px;
        }

        .stats .stat p {
            font-size: 18px;
            color: #666;
        }

        /* Featured Courses */
        .featured-courses {
            text-align: center;
            padding: 50px 20px;
            width: 100%;
        }

        .featured-courses h2 {
            font-size: 36px;
            color: #333;
            margin-bottom: 20px;
        }

        .featured-courses p {
            font-size: 18px;
            color: #666;
            margin-bottom: 40px;
        }

        .courses {
            display: flex;
            justify-content: space-around;
            flex-wrap: wrap;
        }

        .course {
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin: 20px;
            width: 300px;
            text-align: left;
            position: relative;
            padding: 20px;
        }

        .course img {
            width: 100%;
            border-top-left-radius: 10px;
            border-top-right-radius: 10px;
        }

        .course .content {
            padding: 20px;
        }

        .course .content h3 {
            font-size: 20px;
            color: #333;
            margin-bottom: 10px;
        }

        .course .content p {
            font-size: 16px;
            color: #666;
            margin-bottom: 10px;
        }

        .course .content .author,
        .course .content .weeks {
            font-size: 14px;
            color: #999;
        }

        /* Call to Action */
        .cta {
            text-align: center;
            padding: 50px 20px;
            background-color: #f5f7fa;
            width: 100%;
        }

        .cta h2 {
            font-size: 36px;
            color: #333;
            margin-bottom: 20px;
        }

        .cta p {
            font-size: 18px;
            color: #666;
            margin-bottom: 40px;
        }

        .cta .buttons a {
            margin: 0 10px;
            padding: 15px 30px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: 500;
        }

        .cta .buttons .create {
            background-color: #007bff;
            color: #fff;
        }

        .cta .buttons .browse {
            background-color: #fff;
            color: #007bff;
            border: 1px solid #007bff;
        }
    </style>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Hero Section Animation
            gsap.from(".hero .tagline", {
                opacity: 0,
                y: -20,
                duration: 1,
                delay: 0.2
            });
            gsap.from(".hero h1", {
                opacity: 0,
                x: -50,
                duration: 1,
                delay: 0.4
            });
            gsap.from(".hero h2", {
                opacity: 0,
                x: 50,
                duration: 1,
                delay: 0.6
            });
            gsap.from(".hero p", {
                opacity: 0,
                y: 20,
                duration: 1,
                delay: 0.8
            });
            gsap.from(".hero .buttons", {
                opacity: 0,
                scale: 0.8,
                duration: 1,
                delay: 1
            });

            // Stats Section Animation
            gsap.from(".stats .stat", {
                opacity: 0,
                y: 50,
                duration: 1,
                stagger: 0.3,
                delay: 1.2
            });

            // Featured Courses Animation
            gsap.from(".featured-courses h2", {
                opacity: 0,
                y: -20,
                duration: 1,
                delay: 1.5
            });
            gsap.from(".featured-courses .courses .course", {
                opacity: 0,
                scale: 0.9,
                duration: 1,
                stagger: 0.2,
                delay: 1.7
            });

            // Features Section Animation
            gsap.from(".features h2", {
                opacity: 0,
                y: -20,
                duration: 1,
                delay: 2
            });
            gsap.from(".features .feature-list .feature", {
                opacity: 0,
                x: -50,
                duration: 1,
                stagger: 0.2,
                delay: 2.2
            });

            // Testimonials Animation
            gsap.from(".testimonials h2", {
                opacity: 0,
                y: -20,
                duration: 1,
                delay: 2.5
            });
            gsap.from(".testimonials .testimonial-list .testimonial", {
                opacity: 0,
                scale: 0.9,
                duration: 1,
                stagger: 0.3,
                delay: 2.7
            });

            // Call to Action Animation
            gsap.from(".cta h2", {
                opacity: 0,
                y: -20,
                duration: 1,
                delay: 3
            });
            gsap.from(".cta p", {
                opacity: 0,
                y: 20,
                duration: 1,
                delay: 3.2
            });
            gsap.from(".cta .buttons", {
                opacity: 0,
                scale: 0.9,
                duration: 1,
                delay: 3.5
            });
        });
    </script>

    <section class="hero">
        <div class="tagline">🌟 Welcome to the future of online learning 🌟</div>
        <h1>Elevate Your Learning</h1>
        <h2>with Expert Mentors</h2>
        <p>
            Engage with video-based courses, earn certificates, and climb the leaderboard as you master new skills.
            Join a community of passionate learners and expert mentors.
        </p>
        <div class="buttons">
            <a class="explore" href="{{ route('courses.index') }}">Explore Courses</a>
            <a class="create" href="{{ route('register') }}">Create Account</a>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="stats">
        <div class="stat">
            <h3>100+</h3>
            <p>Free Courses</p>
        </div>
        <div class="stat">
            <h3>10k+</h3>
            <p>Active Learners</p>
        </div>
        <div class="stat">
            <h3>500+</h3>
            <p>Expert Mentors</p>
        </div>
        <div class="stat">
            <h3>95%</h3>
            <p>Completion Rate</p>
        </div>
    </section>

    <!-- Featured Courses Section -->
    <section class="featured-courses">
        <h2>Featured Courses</h2>
        <p>
            Explore our most popular courses taught by expert mentors from around the world.
        </p>
        <div class="courses">
            <div class="course">

                <div class="content">
                    <h3>Introduction to Web Development</h3>
                    <p>
                        Learn the fundamentals of HTML, CSS, and JavaScript to build modern web applications.
                    </p>
                    <div class="author">By Sarah Johnson</div>
                </div>
                <div class="badge">Beginner</div>
            </div>
            <div class="course">
                <div class="content">
                    <h3>Advanced Data Science with Python</h3>
                    <p>
                        Master data analysis, visualization, and machine learning techniques using Python.
                    </p>
                    <div class="author">By Michael Chen</div>
                </div>
                <div class="badge">Intermediate</div>
            </div>
            <div class="course">
                <div class="content">
                    <h3>UX/UI Design Principles</h3>
                    <p>
                        Learn how to create intuitive, user-friendly interfaces through research and design.
                    </p>
                    <div class="author">By Emily Rodriguez</div>
                </div>
                <div class="badge">Intermediate</div>
            </div>
            <div class="course">
                <div class="content">
                    <h3>Business Leadership and Management</h3>
                    <p>
                        Develop essential leadership skills to effectively manage teams and drive success.
                    </p>
                    <div class="author">By Robert Williams</div>
                </div>
                <div class="badge">Advanced</div>
            </div>
        </div>
        <div class="view-all">
            <a href="{{ route('courses.index') }}">View All Courses</a>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features">
        <h2>Platform Features</h2>
        <p>
            Discover the tools and features designed to make your learning journey effective, engaging, and rewarding.
        </p>
        <div class="feature-list">
            <div class="feature">
                <h3>Video-Based Learning</h3>
                <p>Engage with immersive video content created by industry experts and educators.</p>
            </div>
            <div class="feature">
                <h3>Earn Certificates</h3>
                <p>Complete courses and quizzes to earn certificates that showcase your achievements.</p>
            </div>
            <div class="feature">
                <h3>Leaderboard Ranking</h3>
                <p>Compete with peers and track your progress on our gamified leaderboard system.</p>
            </div>
            <div class="feature">
                <h3>Community Discussions</h3>
                <p>Participate in course-specific discussions to enhance your learning experience.</p>
            </div>
            <div class="feature">
                <h3>Interactive Quizzes</h3>
                <p>Test your knowledge with MCQ quizzes and ensure comprehensive understanding.</p>
            </div>
            <div class="feature">
                <h3>Curated Content</h3>
                <p>Access quality-controlled content that has been approved by platform administrators.</p>
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section class="testimonials">
        <h2>What Our Learners Say</h2>
        <p>
            Discover how MentorLink has transformed the learning experience for our community.
        </p>
        <div class="testimonial-list">
            <div class="testimonial">
                <p>"The interactive quizzes and video lectures kept me engaged throughout the course. I'm proud to have
                    earned my certificate!"</p>
                <div class="author">Alex Thompson<br>Web Developer</div>
            </div>
            <div class="testimonial">
                <p>"As a mentor, I've found the platform incredibly intuitive. The discussion features allow me to connect
                    with my students in meaningful ways."</p>
                <div class="author">Jessica Liu<br>Data Science Instructor</div>
            </div>
            <div class="testimonial">
                <p>"The gamification elements made learning fun! Competing on the leaderboard motivated me to complete
                    courses faster."</p>
                <div class="author">Marcus Johnson<br>Business Student</div>
            </div>
        </div>
    </section>

    <!-- Call to Action Section -->
    <section class="cta">
        <h2>Ready to Start Your Learning Journey?</h2>
        <p>
            Join thousands of learners already mastering new skills on MentorLink. Sign up today and get access to
            high-quality video courses, interactive quizzes, and a supportive community.
        </p>
        <div class="buttons">
            <a class="create" href="{{ route('register') }}">Create Free Account</a>
            <a class="browse" href="{{ route('courses.index') }}">Browse Courses</a>
        </div>
    </section>
@endsection
