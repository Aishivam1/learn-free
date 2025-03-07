@extends('layouts.app')

@section('title', 'My Courses - MentorLink')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h2 class="text-3xl font-bold text-center">My Enrolled Courses</h2>
    <p class="text-lg text-gray-700 text-center">View all the courses you have enrolled in.</p>

    <!-- Enrolled Courses List -->
    <div class="courses">
        @forelse($enrolledCourses as $course)
            <div class="course">
                <div class="content">
                    <h3>{{ $course->title }}</h3>
                    <p>{{ $course->description }}</p>
                    <p class="author">By {{ $course->mentor->name ?? 'Unknown Mentor' }}</p>
                </div>

                <div class="badge">{{ ucfirst($course->difficulty) }}</div>

                <!-- View Course Button -->
                <a href="{{ route('courses.show', $course->id) }}" class="btn view-btn">View Course</a>
            </div>
        @empty
            <p class="text-center text-gray-500">You have not enrolled in any courses yet.</p>
        @endforelse
    </div>
</div>
@endsection

@push('styles')
<style>
    body {
        font-family: 'Roboto', sans-serif;
        background-color: #f5f7fa;
        color: #333;
    }

    .container {
        text-align: center;
        padding: 50px 20px;
    }

    .courses {
        display: flex;
        justify-content: center;
        flex-wrap: wrap;
        gap: 20px;
        margin-top: 20px;
    }

    .course {
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        background-color: #fff;
        border-radius: 10px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        width: 320px;
        min-height: 350px;
        text-align: left;
        position: relative;
        overflow: hidden;
        transition: transform 0.2s ease-in-out;
    }

    .course:hover {
        transform: scale(1.05);
    }

    .course-img {
        width: 100%;
        height: 180px;
        object-fit: cover;
        border-top-left-radius: 10px;
        border-top-right-radius: 10px;
    }

    .content {
        flex-grow: 1;
        padding: 20px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .content h3 {
        font-size: 20px;
        color: #333;
        margin-bottom: 10px;
    }

    .content p {
        font-size: 16px;
        color: #666;
        margin-bottom: 10px;
        flex-grow: 1; /* Makes description flexible */
    }

    /* .content .author {
        font-size: 14px;
        color: #999;
    } */

    .badge {
        position: absolute;
        top: 15px;
        left: 15px;
        background-color: #007bff;
        color: #fff;
        padding: 5px 10px;
        border-radius: 5px;
        font-size: 12px;
    }

    .btn, .view-btn {
        display: block;
        width: 100%;
        padding: 12px;
        text-align: center;
        background-color: #007bff;
        color: white;
        font-size: 16px;
        font-weight: bold;
        border-radius: 0;
        text-decoration: none;
        transition: background-color 0.3s ease-in-out;
        position: absolute;
        bottom: 0;
    }

    .view-btn:hover {
        background-color: #0056b3;
    }

    @media (max-width: 768px) {
        .courses {
            flex-direction: column;
            align-items: center;
        }
    }
</style>
@endpush
