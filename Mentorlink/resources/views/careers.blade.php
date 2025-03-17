@extends('layouts.app')

@push('style')
    <link rel="stylesheet" href="{{ asset('css/careers.css') }}">
@endpush

@section('content')
    <div class="container text-center">
        <h2>Careers at MentorLink</h2>
        <p>Join our team and help us shape the future of online learning. We are looking for passionate individuals to fill
            various roles in our organization.</p>

        <div class="job-listings d-flex flex-wrap justify-content-center">
            @foreach ([['title' => 'Software Developer', 'description' => 'Develop and maintain our platform.', 'location' => 'Remote'], ['title' => 'Content Creator', 'description' => 'Create engaging educational content.', 'location' => 'Remote'], ['title' => 'Support Team Member', 'description' => 'Help users with queries and issues.', 'location' => 'Remote']] as $job)
                <div class="job-card">
                    <h3>{{ $job['title'] }}</h3>
                    <p>Location: {{ $job['location'] }}</p>
                    <p>{{ $job['description'] }}</p>
                    <button class="apply-btn" data-job="{{ $job['title'] }}">Apply Now</button>
                </div>
            @endforeach
        </div>

        <div class="apply-section" id="applySection" style="display: none;">
            <h3>Apply for <span id="jobTitle"></span></h3>
            <p>Please fill out the form below to apply for the position.</p>
            <form action="#" method="POST">
                @csrf
                <input type="hidden" id="jobPosition" name="job_position">
                <input type="text" name="name" placeholder="Your Name" required>
                <input type="email" name="email" placeholder="Your Email" required>
                <textarea name="message" placeholder="Why do you want to join us?" rows="4" required></textarea>
                <button type="submit">Submit Application</button>
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

        .job-listings {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            margin-bottom: 40px;
        }

        .job-card {
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            width: 300px;
            margin: 20px;
            text-align: left;
            transition: transform 0.3s ease-in-out;
        }

        .job-card:hover {
            transform: scale(1.05);
        }

        .job-card h3 {
            margin-bottom: 10px;
            color: #333;
        }

        .job-card p {
            margin-bottom: 10px;
            color: #666;
        }

        .job-card button {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            background-color: #007bff;
            color: #fff;
            font-size: 16px;
            cursor: pointer;
        }

        .job-card button:hover {
            background-color: #0056b3;
        }

        .apply-section {
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            text-align: left;
        }

        .apply-section h3 {
            margin-bottom: 10px;
            color: #333;
        }

        .apply-section p {
            margin-bottom: 10px;
            color: #666;
        }

        .apply-section form {
            display: flex;
            flex-direction: column;
        }

        .apply-section form input,
        .apply-section form textarea {
            margin-bottom: 10px;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
            width: 100%;
        }

        .apply-section form button {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            background-color: #007bff;
            color: #fff;
            font-size: 16px;
            cursor: pointer;
        }

        .apply-section form button:hover {
            background-color: #0056b3;
        }
    </style>
@endpush
@push('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            document.querySelectorAll('.apply-btn').forEach(button => {
                button.addEventListener('click', function() {
                    let jobTitle = this.getAttribute('data-job');
                    document.getElementById('jobTitle').textContent = jobTitle;
                    document.getElementById('jobPosition').value = jobTitle;
                    document.getElementById('applySection').style.display = 'block';
                });
            });
        });
    </script>
@endpush
