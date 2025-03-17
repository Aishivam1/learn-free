@extends('layouts.app')

@section('head')
    <link rel="stylesheet" href="{{ asset('css/faq.css') }}">
@endsection

@section('content')
    <div class="container">
        <h2>Frequently Asked Questions (FAQ)</h2>

        @foreach ([['title' => 'Signing Up', 'links' => ['How do I sign up for MentorLink?', 'What information do I need to provide during sign-up?', 'Can I sign up using my social media accounts?']], ['title' => 'Taking Courses', 'links' => ['How do I enroll in a course?', 'What are the prerequisites for taking a course?', 'Can I access course materials after completing the course?']], ['title' => 'Earning Certificates', 'links' => ['How do I earn a certificate?', 'Are the certificates recognized by employers?', 'How can I share my certificate on social media?']], ['title' => 'Mentor Qualifications', 'links' => ['Who are the mentors on MentorLink?', 'What qualifications do mentors have?', 'How can I become a mentor?']], ['title' => 'Gamification Features', 'links' => ['What are badges and how do I earn them?', 'How do I earn points?', 'What is the leaderboard and how does it work?']]] as $section)
            <div class="faq-section">
                <h3>{{ $section['title'] }}</h3>
                <ul>
                    @foreach ($section['links'] as $link)
                        <li><a href="#">{{ $link }}</a></li>
                    @endforeach
                </ul>
            </div>
        @endforeach
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
            text-align: left;
        }

        .container h2 {
            margin-bottom: 20px;
            color: #007bff;
            text-align: center;
        }

        .faq-section {
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .faq-section h3 {
            margin-bottom: 10px;
            color: #333;
        }

        .faq-section ul {
            list-style-type: none;
            padding: 0;
        }

        .faq-section ul li {
            background-color: #f1f1f1;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 10px;
        }

        .faq-section ul li a {
            color: #007bff;
            text-decoration: none;
        }

        .faq-section ul li a:hover {
            text-decoration: underline;
        }
    </style>
@endpush
