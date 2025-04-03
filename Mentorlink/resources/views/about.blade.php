@extends('layouts.app')

@section('head')
@endsection

@section('content')
    <div class="container">
        <h2>About MentorLink</h2>
        <p>MentorLink is an innovative learning platform that connects mentors and learners through interactive video-based
            courses.</p>

        <div class="section fade-in">
            <h3>Our Mission</h3>
            <p>To empower learners by providing expert-led courses, fostering a supportive community, and ensuring skill
                development through practical learning.</p>
        </div>

        <div class="section fade-in">
            <h3>Why Choose MentorLink?</h3>
            <ul>
                <li>Learn from experienced mentors</li>
                <li>Track your progress and earn certificates</li>
                <li>Engage in discussions and mentorship programs</li>
                <li>Gamification features for an interactive experience</li>
            </ul>
        </div>

        <div class="section fade-in">
            <h3>Meet Our Team</h3>
            <div class="team-section">
                <div class="team-member">
                    <img src="{{ asset('images/team2.gif') }}" alt="Team Member">
                    <h4>Patel Shivam</h4>
                    <p>Founder,CEO </p>
                </div>
                <div class="team-member">
                    <img src="{{ asset('images/team1.gif') }}" alt="Team Member">
                    <h4>Patel Tirth</h4>
                    <p>Founder,CEO </p>
                </div>
            </div>
        </div>

        <div class="section fade-in">
            <h3>Join Us</h3>
            <p>Start your learning journey today by enrolling in our courses and becoming a part of the MentorLink
                community.</p>
            <a href="{{ route('courses.index') }}" class="btn btn-primary">Explore Courses</a>
        </div>
    </div>
@endsection
@push('styles')
    <style>
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
            text-align: center;
        }

        .section {
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            text-align: left;
            transition: transform 0.3s ease-in-out;
        }

        .section:hover {
            transform: scale(1.02);
        }

        .section h3 {
            margin-bottom: 10px;
            color: #333;
        }

        .section p,
        .section ul {
            color: #666;
        }

        .section ul {
            list-style-type: none;
            padding: 0;
        }

        .section ul li {
            background-color: #f1f1f1;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 10px;
        }

        .team-section {
            display: flex;
            justify-content: center;
            gap: 20px;
        }

        .team-member {
            background: #fff;
            padding: 15px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease-in-out;
        }

        .team-member:hover {
            transform: translateY(-5px);
        }

        .team-member img {
            width: 120px;
            height: 120px;
            border-radius: 50%;
        }

        .fade-in {
            opacity: 0;
            transform: translateY(20px);
            transition: opacity 0.6s ease-out, transform 0.6s ease-out;
        }

        .fade-in.visible {
            opacity: 1;
            transform: translateY(0);
        }
    </style>
@endpush
@push('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            let sections = document.querySelectorAll('.fade-in');

            function reveal() {
                sections.forEach(section => {
                    let windowHeight = window.innerHeight;
                    let sectionTop = section.getBoundingClientRect().top;
                    if (sectionTop < windowHeight - 100) {
                        section.classList.add('visible');
                    }
                });
            }
            window.addEventListener("scroll", reveal);
            reveal();
        });
    </script>
@endpush
