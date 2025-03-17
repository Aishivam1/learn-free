@extends('layouts.app')

@section('title', 'Pending Courses - MentorLink')

@section('content')
    <!-- 3D Books Animation Background -->
    <div class="books-animation">
        <div class="book book1"></div>
        <div class="book book2"></div>
        <div class="book book3"></div>
    </div>

    <div class="create-course-container">
        <a href="{{ route('courses.index') }}" class="back-btn">Back to Courses</a>
    </div>

    <section class="browse-courses">
        <h2>Pending Courses</h2>
        <p>Review and approve or reject courses submitted by mentors.</p>

        <!-- Courses List -->
        <div class="courses">
            @forelse($pendingCourses as $course)
                <div class="course">
                    <div class="content">
                        <h3>{{ $course->title }}</h3>
                        <p>{{ $course->description }}</p>
                        <div class="author">By {{ $course->mentor->name }}</div>
                        <div class="badge">{{ $course->difficulty }}</div>
                    </div>

                    <!-- View Button -->
                    <a href="{{ route('courses.show', ['course' => $course->id, 'from' => 'pending']) }}"
                        class="view-btn">View Details</a>

                    <!-- Approval & Rejection Buttons -->
                    <!-- Approval & Rejection Buttons - Visible Only to Admins -->
                    @if (Auth::user()->role == 'admin')
                        <div class="approval-actions">
                            <form action="{{ route('admin.courses.approve', $course->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <button type="submit" class="approve-btn">✔ Approve</button>
                            </form>

                            <button class="reject-btn" data-course-id="{{ $course->id }}">✖ Reject</button>
                        </div>
                    @endif
                </div>
            @empty
                <p>No pending courses at this time.</p>
            @endforelse
        </div>
    </section>

    <!-- Rejection Modal -->
    <div id="rejectModal" class="modal hidden">
        <div class="modal-content">
            <span class="close-modal">&times;</span>
            <h3>Reject Course</h3>
            <p>Provide a reason for rejecting this course.</p>
            <form id="rejectForm" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="course_id" id="rejectCourseId">
                <textarea name="reason" id="rejectReason" required placeholder="Enter rejection reason..."></textarea>
                <button type="submit" class="reject-confirm-btn">Submit</button>
            </form>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .create-course-container {
            position: absolute;
            left: 0px;
        }

        .back-btn {
            display: inline-block;
            background-color: #3b82f6;
            color: white;
            font-size: 1rem;
            font-weight: bold;
            padding: 0.6rem 1rem;
            border-radius: 0.5rem;
            text-decoration: none;
            margin-bottom: 1.5rem;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        .back-btn:hover {
            background-color: #2563eb;
            transform: translateY(-2px);
        }


        .create-course-btn,
        .approve-btn {
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

        .create-course-btn:hover,
        .approve-btn:hover {
            background-color: #0056b3;
        }

        .browse-courses {
            padding: 50px 20px;
            text-align: center;
        }

        .courses {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 20px;
        }

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

        .course .content {
            padding: 20px;
            margin-top: 25px;
            padding-bottom: 80px;
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

        .badge {
            position: absolute;
            top: 10px;
            left: 20px;
            background-color: #007bff;
            color: #fff;
            padding: 5px 5px;
            border-radius: 5px;
            font-size: 12px;
        }

        .view-btn {
            display: block;
            text-align: center;
            margin: 5px auto;
            padding: 10px;
            font-size: 14px;
            font-weight: bold;
            border-radius: 5px;
            cursor: pointer;
            transition: 0.3s;
        }

        .approval-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
            width: 100%;
            max-width: 300px;
            /* Adjust as needed */
            margin: 10px auto;
        }

        .approval-actions form {
            display: flex;
            /* Ensures button inside form behaves like the reject button */
            flex: 1;
            /* Makes both elements (form and button) take equal width */
        }

        .approve-btn,
        .reject-btn {
            flex: 1;
            width: 100%;
            /* Ensures both buttons take full width inside their container */
            margin: 5px 0;
            padding: 10px;
            font-size: 14px;
            font-weight: bold;
            border-radius: 5px;
            cursor: pointer;
            transition: 0.3s;
            text-align: center;
        }

        .view-btn {
            background: white;
            color: #007bff;
            border: 1px solid #007bff;
        }

        .view-btn:hover {
            background-color: #007bff;
            color: white;
        }

        .reject-btn {
            background: rgb(226, 21, 21);
            color: white;
            border: none;
        }

        .reject-btn:hover {
            background: darkred;
        }

        /* Modal Styling */
        .modal {
            display: flex;
            justify-content: center;
            align-items: center;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
        }

        .modal-content {
            background: white;
            padding: 20px;
            border-radius: 8px;
            width: 400px;
            text-align: center;
        }

        .modal textarea {
            width: 100%;
            height: 80px;
            margin-top: 10px;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .reject-confirm-btn {
            background: red;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .reject-confirm-btn:hover {
            background: darkred;
        }

        .close-modal {
            float: right;
            font-size: 20px;
            cursor: pointer;
        }

        .hidden {
            display: none;
        }

        .my-4 {
            margin-top: 0 !important;
            margin-bottom: 1.5rem !important;
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const rejectButtons = document.querySelectorAll('.reject-btn');
            const rejectModal = document.getElementById('rejectModal');
            const rejectCourseId = document.getElementById('rejectCourseId');
            const rejectForm = document.getElementById('rejectForm');
            const closeModal = document.querySelector('.close-modal');

            // Show rejection modal
            rejectButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const courseId = this.getAttribute('data-course-id');
                    rejectCourseId.value = courseId;

                    // Debugging
                    console.log(`Reject Button Clicked: Course ID = ${courseId}`);

                    rejectForm.action = `${window.location.origin}/admin/courses/${courseId}/reject`;
                    console.log(`Form Action Set: ${rejectForm.action}`);

                    rejectModal.classList.remove('hidden');
                });
            });

            // Close modal
            closeModal.addEventListener('click', function() {
                console.log("Closing Modal");
                rejectModal.classList.add('hidden');
            });

            // Close modal on background click
            window.addEventListener('click', function(event) {
                if (event.target === rejectModal) {
                    console.log("Clicked outside modal, closing");
                    rejectModal.classList.add('hidden');
                }
            });
        });
    </script>
@endpush
