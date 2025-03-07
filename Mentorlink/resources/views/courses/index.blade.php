@extends('layouts.app')

@section('title', 'Browse Courses - MentorLink')

@section('content')
    <!-- 3D Books Animation Background -->
    <div class="books-animation">
        <div class="book book1"></div>
        <div class="book book2"></div>
        <div class="book book3"></div>
    </div>
    @if (Auth::user()->role === 'mentor')
        <div class="create-course-container">
            <a href="{{ route('courses.create') }}" class="create-course-btn">+ Create Course</a>
        </div>
    @endif


    <section class="browse-courses">
        <h2>Browse Courses</h2>
        <p>View all approved courses, filter by category, mentor, and difficulty.</p>

        <!-- Filters -->
        <div class="filters">
            <select id="category-filter">
                <option value="">Category</option>
                @foreach ($categories as $category)
                    <option value="{{ $category }}">{{ $category }}</option>
                @endforeach
            </select>
            @if (Auth::user()->role !== 'mentor') <select id="mentor-filter">
                    <option value="">Mentor</option>
                    @foreach ($mentors as $mentor)
                        <option value="{{ $mentor }}">{{ $mentor }}</option>
                    @endforeach
                </select>
            @endif
            <select id="difficulty-filter">
                <option value="">Difficulty</option>
                @foreach ($difficulties as $difficulty)
                    <option value="{{ $difficulty }}">{{ $difficulty }}</option>
                @endforeach
            </select>
        </div>

        <!-- Courses List -->
        <div class="courses">
            @forelse($courses as $course)
                <div class="course" data-category="{{ $course->category }}" data-mentor="{{ $course->mentor->name }}"
                    data-difficulty="{{ $course->difficulty }}">

                    <div class="content">
                        <h3>{{ $course->title }}</h3>
                        <p>{{ $course->description }}</p>
                        <div class="author">By {{ $course->mentor->name }}</div>
                    </div>
                    <div class="badge">{{ $course->difficulty }}</div>

                    <!-- View Button -->
                    <a href="{{ route('courses.show', $course->id) }}" class="view-btn">View Details</a>

                    <!-- Enroll Button -->
                    @if (Auth::user()->role == 'learner')
                        <form action="{{ route('courses.enroll', $course->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="enroll-btn">Enroll</button>
                        </form>
                    @endif
                </div>

            @empty
                <p>No courses available at this time.</p>
            @endforelse
        </div>
    </section>
@endsection

@push('styles')
    <style>
        .create-course-container {
            position: absolute;
            right: 0px;
        }

        .create-course-btn {
            display: inline-block;
            padding: 12px 20px;
            font-size: 16px;
            font-weight: bold;
            color: white;
            background-color: #007bff;
            border-radius: 8px;
            text-decoration: none;
            transition: background-color 0.3s ease-in-out;
            box-shadow: 0px 4px 8px rgba(0, 123, 255, 0.2);
        }

        .create-course-btn:hover {
            background-color: #0056b3;
        }

        /* Browse Courses Section */
        .browse-courses {
            padding: 50px 20px;
            text-align: center;
        }

        .browse-courses h2 {
            font-size: 36px;
            color: #333;
            margin-bottom: 20px;
        }

        .browse-courses p {
            font-size: 18px;
            color: #666;
            margin-bottom: 40px;
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
            text-align: left;
            position: relative;
            overflow: hidden;
            transition: transform 0.2s ease-in-out;
            padding-bottom: 60px;
        }

        .course:hover {
            transform: scale(1.05);
        }

        /* Course Content */
        .course .content {
            padding: 20px;
            padding-bottom: 80px;
            position: relative;
        }


        .course .content h3 {
            font-size: 20px;
            color: #333;
            margin-bottom: 10px;
        }

        .course .content p {
            font-size: 16px;
            color: #666;
            margin-bottom: 10px;
        }

        .course .content .author {
            font-size: 14px;
            color: #999;
        }

        /* Difficulty Badge */
        .course .badge {
            position: absolute;
            top: 20px;
            left: 20px;
            background-color: #007bff;
            color: #fff;
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 12px;
        }

        /* View Details Button */
        .view-btn {
            position: absolute;
            bottom: 50px;
            /* Moves it above the enroll button */
            left: 50%;
            transform: translateX(-50%);
            width: 90%;
            padding: 12px;
            background-color: white;
            color: #007bff;
            font-size: 16px;
            font-weight: bold;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            text-align: center;
            text-decoration: none;
            transition: background-color 0.3s ease-in-out, transform 0.2s;
            box-shadow: 0px 4px 8px rgba(40, 167, 69, 0.2);
        }

        .view-btn:hover {
            /* background-color: #007bff;
                        color: white; */
            transform: translateX(-50%) translateY(-2px);
        }

        /* Enroll Button */
        .enroll-btn {
            position: absolute;
            bottom: 10px;
            left: 50%;
            transform: translateX(-50%);
            width: 90%;
            padding: 12px;
            background-color: #007bff;
            color: white;
            font-size: 16px;
            font-weight: bold;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            text-align: center;
            transition: background-color 0.3s ease-in-out;
            box-shadow: 0px 4px 8px rgba(0, 123, 255, 0.2);
        }

        .enroll-btn:hover {
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

            .enroll-btn {
                background-color: #0056b3;
                box-shadow: 0px 4px 8px rgba(0, 123, 255, 0.3);
            }

            .enroll-btn:hover {
                background-color: #004080;
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
