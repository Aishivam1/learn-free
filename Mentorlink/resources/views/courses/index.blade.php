@extends('layouts.app')

@section('title', 'Browse Courses - MentorLink')

@section('content')
    <!-- 3D Books Animation Background -->
    <div class="books-animation">
        <div class="book book1"></div>
        <div class="book book2"></div>
        <div class="book book3"></div>
    </div>
    <div class="create-course-container">
        @if (Auth::user()->role === 'learner')
            <a href="{{ route(name: 'courses.my') }}" class="create-course-btn">My Course</a>
        @endif
        @if (Auth::user()->role === 'mentor')
            <a href="{{ route('courses.create') }}" class="create-course-btn">+ Create Course</a>
            <a href="{{ route('courses.rejected') }}" class="back-btn">View Rejected Courses</a>
        @endif
        @if (Auth::user()->role !== 'learner')
            <a href="{{ route('admin.courses.pending') }}" class="create-course-btn">Pending Courses</a>
        @endif
    </div>

    <section class="browse-courses">
        <h2>Browse Courses</h2>
        <p>View all approved courses, filter by category
            @if (Auth::user()->role !== 'mentor')
                , mentor,
            @endif
            and difficulty.
        </p>

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
                    <option value="{{ $difficulty }}">{{ $difficulty }}</option>
                @endforeach
            </select>
        </div>

        <!-- Courses List -->
        <div class="courses">
            @forelse($courses as $course)
                <div class="course" data-category="{{ $course->category }}" data-mentor="{{ $course->mentor->name }}"
                    data-difficulty="{{ $course->difficulty }}">

                    <!-- Delete Button (Only for the course creator) -->
                    @if (Auth::user()->role === 'mentor' && Auth::id() === $course->mentor_id)
                        <form action="{{ route('courses.destroy', $course->id) }}" method="POST" class="delete-form">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="delete-btn">✖</button>
                        </form>
                    @endif

                    <div class="content">
                        <h3>{{ $course->title }}</h3>
                        <p>{{ $course->description }}</p>
                        <div class="author">By {{ $course->mentor->name }}</div>
                    </div>
                    <div class="badge">{{ $course->difficulty }}</div>
                    @if (Auth::user()->role === 'mentor' && Auth::user()->id === $course->mentor_id)
                        <a href="{{ route('courses.quiz.create', $course->id) }}" class="create-course-btn">
                            <i class="fas fa-plus"></i>Add Quiz
                        </a>
                    @endif
                    <!-- View Button -->
                    <a href="{{ route('courses.show', ['course' => $course->id, 'from' => 'browse']) }}"
                        class="view-btn">View Details</a>

                    <!-- Enroll Button -->
                    @if (Auth::user()->role == 'mentor' && Auth::id() === $course->mentor_id)
                        <!-- Update Button for Mentors -->
                        <a href="{{ route('courses.edit', $course->id) }}" class="enroll-btn">Update</a>
                    @elseif (Auth::user()->role == 'learner')
                        @php
                            $isEnrolled = $course->enrollments()->where('user_id', Auth::id())->exists();
                        @endphp

                        @if ($isEnrolled)
                            <button class="enroll-btn enrolled" disabled>Already Enrolled</button>
                        @else
                            <form action="{{ route('courses.enroll', $course->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="enroll-btn">Enroll</button>
                            </form>
                        @endif
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
        /* Delete Button */
        .delete-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            background: red;
            color: white;
            border: none;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            font-size: 16px;
            cursor: pointer;
            transition: background 0.3s ease-in-out;
            z-index: 10;
        }

        .delete-btn:hover {
            background: darkred;
        }

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
            text-align: center;
            transition: background-color 0.3s ease-in-out;
            box-shadow: 0px 4px 8px rgba(0, 123, 255, 0.2);
            margin-bottom: 35px;
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
            padding: 40px 20px 80px;
            /* Increased top padding */
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
            document.querySelectorAll('.delete-form').forEach(form => {
                form.addEventListener('submit', function(event) {
                    event.preventDefault(); // Stop the form from submitting immediately

                    Swal.fire({
                        title: "Are you sure?",
                        text: "This action cannot be undone!",
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#d33",
                        cancelButtonColor: "#3085d6",
                        confirmButtonText: "Yes, delete it!",
                        cancelButtonText: "Cancel"
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit(); // Submit the form if the user confirms
                        }
                    });
                });
            });
        });

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
