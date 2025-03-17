@extends('layouts.app')

@section('title', 'Rejected Courses - MentorLink')

@section('content')
    <div class="create-course-container">
        <a href="{{ route('courses.index') }}" class="back-btn">Back to Courses</a>
    </div>

    <section class="browse-courses">
        <h2>Rejected Courses</h2>
        <p>These courses were rejected. Check the reason and make the necessary improvements.</p>

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
