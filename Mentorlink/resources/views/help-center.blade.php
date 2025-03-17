@extends('layouts.app')

@section('head')
    <link rel="stylesheet" href="{{ asset('css/help-center.css') }}">
@endsection

@section('content')
    <div class="container">
        <h2>Help Center</h2>

        @foreach ([['title' => 'Course Enrollment', 'links' => ['How to Enroll in a Course', 'Understanding Course Levels', 'Managing Your Enrollments']], ['title' => 'Mentor Interactions', 'links' => ['How to Contact Your Mentor', 'Participating in Mentor Sessions', 'Providing Feedback to Mentors']], ['title' => 'Video Playback Issues', 'links' => ['Troubleshooting Video Playback', 'Supported Browsers and Devices', 'Adjusting Video Quality']], ['title' => 'Payment Troubleshooting', 'links' => ['How to Make a Payment', 'Handling Payment Failures', 'Requesting a Refund']], ['title' => 'Account Management', 'links' => ['Updating Your Profile', 'Changing Your Password', 'Deactivating Your Account']]] as $section)
            <div class="section">
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
            text-align: center;
        }

        .container h2 {
            margin-bottom: 20px;
            color: #007bff;
        }

        .section {
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            text-align: left;
        }

        .section h3 {
            margin-bottom: 10px;
            color: #333;
        }

        .section p {
            margin-bottom: 10px;
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

        .section ul li a {
            color: #007bff;
            text-decoration: none;
        }

        .section ul li a:hover {
            text-decoration: underline;
        }
    </style>
@endpush
