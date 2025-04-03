@extends('layouts.app')

@section('title', 'My Courses - MentorLink')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="back-btn-container">
            <a href="{{ route('courses.index') }}" class="btn btn-my-course">Back to All Courses</a>
        </div>

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
                            <div class="author">By {{ $course->mentor->name ?? 'Unknown Mentor' }}</div>
                        </div>

                        <div class="badge">{{ ucfirst($course->difficulty) }}</div>

                        <!-- View Course Button -->
                        <a href="{{ route('courses.show', $course->id) }}" class="btn btn-view">View Details</a>
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
        
      .create-course-container {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-bottom: 20px;
        }

        .container {
            text-align: center;
            padding: 0px 20px;
        }

        /* General Button Styles */
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

        .back-btn-container .btn-my-course {
            background-color: #007bff;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            transition: background-color 0.3s ease;
        }

        .back-btn-container .btn-my-course:hover {
            background-color: #0056b3;
        }

        .back-btn-container {
            display: flex;
            justify-content: flex-start;
            margin-bottom: 20px;
        }

        /* Filters */
        .filters {
            display: flex;
            justify-content: center;
            margin-bottom: 40px;
            flex-wrap: wrap;
        }

        .filters select {
            margin: 0 10px;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }

        /* Course Grid */
        .courses {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 20px;
        }

        /* Course Card */
        .course {
            display: flex;
            flex-direction: column;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            width: 300px;
            height: 420px;
            text-align: left;
            position: relative;
            overflow: hidden;
            transition: transform 0.2s ease-in-out;
            padding-bottom: 60px;
        }

        .course:hover {
            transform: scale(1.05);
        }

        .course .content {
            padding: 40px 20px;
            position: relative;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .course .content h3 {
            font-size: 20px;
            color: #333;
            margin-bottom: 10px;
            height: 48px;
            /* Fixed height for title */
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .course .content p {
            font-size: 16px;
            color: #666;
            margin-bottom: 10px;
            height: 50px;
            /* Fixed height for description */
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .course .content .author {
            font-size: 14px;
            color: #999;
        }

        /* Difficulty Badge */
        .course .badge {
            position: absolute;
            top: 10px;
            left: 10px;
            background-color: #007bff;
            color: #fff;
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 14px;
            font-weight: bold;
            z-index: 5;
        }

        /* View Button */
        .btn-view {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 0;
            padding: 12px 0;
        }

        .btn-view:hover {
            background-color: #0056b3;
        }

        /* Dark Mode */
        @media (prefers-color-scheme: dark) {
            body {
                background-color: #121212;
                color: #ddd;
            }

            .course {
                background-color: #1e1e1e;
                color: #ddd;
            }

            .course .content h3 {
                color: #eee;
            }

            .course .content p {
                color: #bbb;
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
