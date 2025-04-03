@extends('layouts.app')

@section('content')
    <div class="container text-center">
        <h2>Press</h2>

        @foreach([
            ['title' => 'Announcements', 'content' => 'Stay updated with the latest news from MentorLink. We are excited to announce the launch of our new course on Advanced Data Science with Python. Enroll now!'],
            ['title' => 'Partnerships', 'content' => 'MentorLink has partnered with multiple company to provide exclusive content and resources for our learners.'],
            ['title' => 'Platform Milestones', 'content' => 'We have reached 10,000 active learners! Thank you for being a part of our community.']
        ] as $section)
            <div class="section">
                <h3>{{ $section['title'] }}</h3>
                <p>{{ $section['content'] }}</p>
            </div>
        @endforeach

        <div class="media-inquiries">
            <h3>Media Inquiries</h3>
            <p>If you have any media inquiries, please fill out the form below, and our team will get back to you shortly.</p>
            <form action="#" method="POST">
                @csrf
                <input type="text" name="name" placeholder="Your Name" required>
                <input type="email" name="email" placeholder="Your Email" required>
                <textarea name="message" placeholder="Your Message" rows="4" required></textarea>
                <button type="submit">Submit Inquiry</button>
            </form>
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
        }

        .section,
        .media-inquiries {
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            text-align: left;
        }

        .section h3,
        .media-inquiries h3 {
            margin-bottom: 10px;
            color: #333;
        }

        .section p,
        .media-inquiries p {
            margin-bottom: 10px;
            color: #666;
        }

        .media-inquiries form {
            display: flex;
            flex-direction: column;
        }

        .media-inquiries form input,
        .media-inquiries form textarea {
            margin-bottom: 10px;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
            width: 100%;
        }

        .media-inquiries form button {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            background-color: #007bff;
            color: #fff;
            font-size: 16px;
            cursor: pointer;
        }

        .media-inquiries form button:hover {
            background-color: #0056b3;
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            document.querySelectorAll('form').forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    alert('Thank you for your inquiry! We will get back to you soon.');
                    this.reset();
                });
            });
        });
    </script>
@endpush
