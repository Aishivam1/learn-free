@extends('layouts.app')

@section('head')
    <link rel="stylesheet" href="{{ asset('css/terms-of-service.css') }}">
@endsection

@section('content')
    <div class="container">
        <h2>Terms of Service</h2>

        @foreach ([
            ['title' => 'Course Access', 'content' => 'By enrolling in a course on MentorLink, you gain access to the course materials for the duration specified in the course details. Access to the course materials is for personal use only and cannot be shared with others.'],
            ['title' => 'Content Policies', 'content' => 'All content provided on MentorLink is the intellectual property of MentorLink or its content creators. You may not reproduce, distribute, or create derivative works from any content without explicit permission.', 'points' => ['Respect copyright and intellectual property rights.', 'Do not share or distribute course materials without permission.', 'Report any content that violates these policies.']],
            ['title' => 'Refund Policies', 'content' => 'We offer refunds for courses under certain conditions. Please review our refund policy below:', 'points' => ['Refund requests must be made within 14 days of purchase.', 'Refunds are not available if more than 50% of the course has been completed.', 'To request a refund, contact our support team with your order details.']],
            ['title' => 'Code of Conduct', 'content' => 'We are committed to providing a safe and respectful learning environment for all users. By using MentorLink, you agree to adhere to the following code of conduct:', 'points' => ['Be respectful and considerate to others.', 'Do not engage in harassment, discrimination, or hate speech.', 'Report any behavior that violates this code of conduct.', 'Follow all applicable laws and regulations.']],
        ] as $section)
            <div class="section">
                <h3>{{ $section['title'] }}</h3>
                <p>{{ $section['content'] }}</p>
                @if (isset($section['points']))
                    <ul>
                        @foreach ($section['points'] as $point)
                            <li>{{ $point }}</li>
                        @endforeach
                    </ul>
                @endif
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

        .section {
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
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
            list-style-type: disc;
            padding-left: 20px;
        }

        .section ul li {
            margin-bottom: 10px;
        }
    </style>
@endpush
