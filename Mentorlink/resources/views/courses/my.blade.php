@extends('layouts.app')

@section('title', 'My Courses - MentorLink')

@section('content')

    <div class="container mx-auto px-4 py-8">
        <a href="{{ route('courses.index') }}" class="back-btn">Back to All Courses</a>
        <h2 class="text-3xl font-bold text-center">My Enrolled Courses</h2>
        <p class="text-lg text-gray-700 text-center">View all the courses you have enrolled in.</p>

        <!-- Filters -->
        <div class="filters">
            <select id="category-filter">
                <option value="">Category</option>
                @foreach ($categories as $category)
                    <option value="{{ $category }}">{{ $category }}</option>
                @endforeach
            </select>

            @if (Auth::user()->role !== 'mentor')
                <select id="mentor-filter">
                    <option value="">Mentor</option>
                    @foreach ($mentors as $mentor)
                        <option value="{{ $mentor }}">{{ $mentor }}</option>
                    @endforeach
                </select>
            @endif

            <select id="difficulty-filter">
                <option value="">Difficulty</option>
                @foreach ($difficulties as $difficulty)
                    <option value="{{ $difficulty }}">{{ ucfirst($difficulty) }}</option>
                @endforeach
            </select>
        </div>

        <!-- Enrolled Courses List -->
        <div class="courses">
            @forelse($enrolledCourses as $course)
                @if (Auth::user()->role !== 'mentor' || Auth::id() === $course->mentor_id)
                    <div class="course" data-category="{{ $course->category }}"
                        data-mentor="{{ $course->mentor->name ?? 'Unknown Mentor' }}"
                        data-difficulty="{{ $course->difficulty }}">

                        <div class="content">
                            <h3>{{ $course->title }}</h3>
                            <p>{{ $course->description }}</p>
                            <p class="author">By {{ $course->mentor->name ?? 'Unknown Mentor' }}</p>
                        </div>

                        <div class="badge">{{ ucfirst($course->difficulty) }}</div>

                        <!-- View Course Button -->
                        <a href="{{ route('courses.show', $course->id) }}" class="btn view-btn">View Course</a>
                    </div>
                @endif
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

        .back-btn {
            margin-top: 10px;
            margin-left: 10px;
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            display: inline-block;
            transition: background-color 0.3s ease-in-out;
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
            flex-grow: 1;
            /* Makes description flexible */
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

        .btn,
        .view-btn {
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
@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            console.log("Browse Courses page loaded.");

            const categoryFilter = document.getElementById('category-filter');
            const mentorFilter = document.getElementById('mentor-filter');
            const difficultyFilter = document.getElementById('difficulty-filter');
            const courses = document.querySelectorAll('.course');

            function filterCourses() {
                const selectedCategory = categoryFilter.value.toLowerCase();
                const selectedMentor = mentorFilter.value.toLowerCase();
                const selectedDifficulty = difficultyFilter.value.toLowerCase();

                courses.forEach(course => {
                    const courseCategory = course.getAttribute('data-category').toLowerCase();
                    const courseMentor = course.getAttribute('data-mentor').toLowerCase();
                    const courseDifficulty = course.getAttribute('data-difficulty').toLowerCase();

                    const categoryMatch = !selectedCategory || courseCategory === selectedCategory;
                    const mentorMatch = !selectedMentor || courseMentor === selectedMentor;
                    const difficultyMatch = !selectedDifficulty || courseDifficulty === selectedDifficulty;

                    course.style.display = (categoryMatch && mentorMatch && difficultyMatch) ? 'block' :
                        'none';
                });
            }

            categoryFilter.addEventListener('change', filterCourses);
            mentorFilter.addEventListener('change', filterCourses);
            difficultyFilter.addEventListener('change', filterCourses);
        });
    </script>
@endpush
