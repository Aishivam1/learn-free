@extends('layouts.app')

@section('title', 'Rejected Courses - MentorLink')

@section('content')
    <div class="create-course-container">
        <a href="{{ route('courses.index') }}" class="btn btn-my-course">Back to Courses</a>
    </div>

    <section class="browse-courses">
       <div> <h2>Rejected Courses</h2></div>
        <div><p>These courses were rejected. Check the reason and make the necessary improvements.</p></div>

        <div class="courses">
            @forelse($rejectedCourses as $course)
                <div class="course">
                    <div class="content">
                        <h3>{{ $course->title }}</h3>
                        <p>{{ $course->description }}</p>
                        <div class="author">By You</div>
                        <div class="badge">{{ $course->difficulty }}</div>
                        <div class="rejection-reason">
                            <strong>Rejection Reason:</strong> {{ $course->rejection_reason }}
                        </div>
                    </div>

                    <a href="{{ route('courses.edit', $course->id) }}" class="view-btn">Edit & Resubmit</a>
                </div>
            @empty
                <p>No rejected courses at this time.</p>
            @endforelse
        </div>
    </section>
@endsection

@push('styles')
    <style>
        .btn {
            display: inline-block;
            padding: 12px 20px;
            font-size: 16px;
            font-weight: bold;
            border-radius: 8px;
            text-decoration: none;
            text-align: center;
            transition: all 0.3s ease-in-out;
        }

        .btn-my-course {
            background-color: #007bff;
            color: white;
            margin-right: 0px;

        }

        .browse-courses {
            display: flex!important;
            flex-direction: column;
            align-items: center;
            justify-content: center;}

        .btn-my-course:hover {
            background-color: #0056b3;
        }
    </style>
@endpush
