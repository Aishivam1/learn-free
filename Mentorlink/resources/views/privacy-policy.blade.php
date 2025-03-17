@extends('layouts.app')

@section('head')
    <link rel="stylesheet" href="{{ asset('css/privacy-policy.css') }}">
@endsection

@section('content')
    <div class="container">
        <h2>Privacy Policy</h2>

        @foreach ([
            ['title' => 'User Data', 'content' => 'MentorLink is committed to protecting your privacy. We collect and use your personal data only as necessary to provide our services and improve your experience.', 'points' => ['We collect information you provide directly, such as when you create an account, enroll in a course, or contact support.', 'We also collect information automatically, such as your IP address, browser type, and usage data.', 'We use your data to personalize your experience, communicate with you, and improve our services.']],
            ['title' => 'Cookies', 'content' => 'We use cookies and similar technologies to enhance your experience on MentorLink. Cookies are small data files stored on your device that help us remember your preferences and understand how you use our platform.', 'points' => ['You can control cookies through your browser settings and other tools.', 'Disabling cookies may affect the functionality of our platform.']],
            ['title' => 'Payment Security', 'content' => 'We take payment security seriously and use industry-standard measures to protect your payment information.', 'points' => ['All payment transactions are encrypted using secure socket layer (SSL) technology.', 'We do not store your payment information on our servers.', 'We use trusted third-party payment processors to handle transactions.']],
            ['title' => 'Third-Party Integrations', 'content' => 'MentorLink may integrate with third-party services to enhance your experience. These services may have their own privacy policies, and we encourage you to review them.', 'points' => ['We share your data with third parties only as necessary to provide our services and comply with legal obligations.', 'We do not sell your personal data to third parties.', 'We take steps to ensure that third-party services protect your data in accordance with our privacy policy.']],
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
