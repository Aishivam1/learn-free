@extends('layouts.app')

@section('title', $course->title . ' - Course Details')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Back to Courses Button -->
    @if($course->enrollments->contains('user_id', auth()->id()))
    <a href="{{ route('courses.my') }}" class="btn back-btn">My Courses</a>
@else
    <a href="{{ route('courses.index') }}" class="btn back-btn">Back to All Courses</a>
@endif


    <div class="course-details space-y-8">
        <!-- Course Title & Meta -->
        <h1 class="text-3xl font-bold text-center">Title: {{ $course->title }}</h1>
        <h1 class="text-3xl font-bold text-center"><strong>👨‍🏫 Mentor:</strong> {{ $course->mentor->name }}</h1>
        <p class="text-lg text-gray-700 text-center">Description: {{ $course->description }}</p>
        <p class="text-md text-gray-600 text-center"><strong>📊 Difficulty:</strong>
            <span class="px-3 py-1 rounded-full text-white
                {{ $course->difficulty === 'beginner' ? 'bg-green-500' : ($course->difficulty === 'intermediate' ? 'bg-yellow-500' : 'bg-red-500') }}">
                {{ ucfirst($course->difficulty) }}
            </span></p>

        <!-- Course Materials Section -->
        <div class="materials-list">
            <h2 class="text-2xl font-semibold mb-4 text-center">📂 Course Materials</h2>

            @if ($course->materials->isEmpty())
                <p class="text-gray-500 text-center">No materials available for this course.</p>
            @else
                <ul class="list-disc pl-6 space-y-2">
                    @foreach ($course->materials as $material)
                        <li class="bg-white shadow-md rounded-md p-4">
                            <strong>{{ $material->name }}</strong> ({{ ucfirst($material->type) }})
                            @if ($material->file_path)
                                - <a href="{{ asset('storage/' . $material->file_path) }}" class="text-blue-500 hover:underline" target="_blank">Download</a>
                            @endif
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
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

    .course-details {
        text-align: center;
        padding: 50px 20px;
    }

    .course-details h1 {
        font-size: 36px;
        margin-bottom: 10px;
    }

    .course-details p {
        font-size: 18px;
        color: #666;
        margin-bottom: 20px;
    }

    .materials-list {
        margin-top: 40px;
    }

    .materials-list h2 {
        font-size: 24px;
        color: #333;
        margin-bottom: 20px;
    }

    .materials-list ul {
        list-style-type: none;
        padding: 0;
    }

    .materials-list ul li {
        background-color: #fff;
        padding: 15px;
        border-radius: 5px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        margin-bottom: 10px;
        font-size: 16px;
        color: #666;
    }

    /* Back Button */
    .back-btn {
        display: inline-block;
        background-color: #007bff;
        color: white;
        font-size: 16px;
        font-weight: bold;
        padding: 10px 15px;
        border-radius: 8px;
        transition: background-color 0.3s ease-in-out, transform 0.2s;
    }

    .back-btn:hover {
        background-color: #0056b3;
        transform: translateY(-2px);
    }
</style>
@endpush

@push('scripts')
<script>
    gsap.from(".course-details", {
        duration: 1,
        opacity: 0,
        y: 50,
        ease: "power2.out"
    });
</script>
@endpush
