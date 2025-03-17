@extends('layouts.app')

@section('title', $course->title . ' - Course Details')
<meta name="csrf-token" content="{{ csrf_token() }}">
@section('content')
    @if ($course->enrollments->contains('user_id', auth()->id()))
        <a href="{{ route('courses.my') }}" class="back-btn">My Courses</a>
    @else
        <a href="{{ route(request('from') === 'pending' ? 'admin.courses.pending' : 'courses.index') }}" class="back-btn">Back
            to All Courses</a>
    @endif
    <div class="container">
        <!-- Hidden input for course ID -->
        <input type="hidden" name="course_id" value="{{ $course->id }}">

        <!-- Back to Courses Button -->


        <div class="course-details">
            <h1 class="course-title">Title: {{ $course->title }}</h1>
            <h1 class="course-mentor"><strong>👨‍🏫 Mentor:</strong> {{ $course->mentor->name }}</h1>
            <p class="course-description">Description: {{ $course->description }}</p>
            <p class="course-difficulty"><strong>📊 Difficulty:</strong>
                <span class="difficulty-badge difficulty-{{ $course->difficulty }}">
                    {{ ucfirst($course->difficulty) }}
                </span>
            </p>

            <!-- Single Video Player Section - Initially Hidden -->
            <div id="video-container" class="video-container">
                <h3 id="current-video-title" class="video-title"></h3>
                <button id="close-video-btn" class="close-btn">✖</button>

                <div class="custom-video-wrapper">
                    <video id="videoPlayer" class="video-player" oncontextmenu="return false;">
                        <source id="videoSource" src="" type="video/mp4">
                        Your browser does not support the video tag.
                    </video>
                    <input type="hidden" id="videoId" value="">
                    <!-- Custom Video Controls -->
                    <div class="custom-controls">
                        <button id="playPauseBtn" class="control-btn">▶️</button>
                        <input type="range" id="volumeSlider" min="0" max="1" step="0.1" value="1"
                            class="volume-slider">
                        <button id="fullscreenBtn" class="control-btn">⛶</button>
                    </div>
                </div>

                <!-- Progress Bar for the active video -->
                <div class="progress-container">
                    <div id="activeProgressBar" class="progress-bar"></div>
                </div>
            </div>

            <div class="materials-list">
                <h2 class="materials-title">📂 Course Materials</h2>

                <h3 class="section-title">🎬 Videos</h3>
                @php
                    $videos = $course->materials->where('type', 'video');
                @endphp

                @if ($videos->isEmpty())
                    <p class="no-content">No videos available for this course.</p>
                @else
                    <ul class="materials-items">
                        @foreach ($videos as $video)
                            <li class="material-item">
                                <div class="material-header">
                                    <strong>{{ $video->name }}</strong>
                                    <button class="play-btn" data-video="{{ route('materials.video', $video->id) }}"
                                        data-id="{{ $video->id }}" data-title="{{ $video->name }}">
                                        ▶️ Play
                                    </button>
                                </div>

                                <!-- Individual Progress Bar -->
                                <div class="progress-container">
                                    <div id="progressBar-{{ $video->id }}" class="progress-bar"
                                        style="width: {{ $video->progress ?? 0 }}%;"></div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif

                <h3 class="section-title">📄 Materials</h3>
                @php
                    $pdfs = $course->materials->where('type', 'pdf');
                @endphp

                @if ($pdfs->isEmpty())
                    <p class="no-content">No PDFs available for this course.</p>
                @else
                    <ul class="materials-items">
                        @foreach ($pdfs as $pdf)
                            <li class="material-item">
                                <div class="material-header">
                                    <strong>{{ $pdf->name }}</strong>
                                    <div class="pdf-actions">
                                        <!-- View PDF Button -->
                                        <button class="view-pdf-btn" data-pdf="{{ route('materials.pdf', $pdf->id) }}"
                                            data-name="{{ $pdf->name }}">
                                            📖 View PDF
                                        </button>

                                        <!-- Download PDF Button -->
                                        <a href="{{ route('materials.pdf', $pdf->id) }}" class="download-btn" download>
                                            ⬇ Download
                                        </a>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
        @if (Auth::user()->role === 'learner')
            <div class="quiz-section">
                @if (
                    $course->enrollments()->where('user_id', auth()->id())->where('progress', 100)->exists() &&
                        $course->quizAttempts()->where('user_id', auth()->id())->count() < 3)
                    <a href="{{ route('quiz.attempt', $course->id) }}" class="quiz-btn">📝 Attend Quiz</a>
                @elseif (!$course->enrollments()->where('user_id', auth()->id())->where('progress', 100)->exists())
                    <p class="alert-message">
                        <i class="alert-icon"></i> You need to complete the course to attend the quiz.
                    </p>
                @else
                    <p class="alert-message">
                        <i class="alert-icon"></i> You have reached the maximum quiz attempts (3).
                    </p>
                @endif
            </div>
        @endif


        <div class="feedback-section">
            <h3 class="feedback-title">📢 Student Feedback</h3>

            @if ($course->feedback->count() > 0)
                @foreach ($course->feedback as $feedback)
                    <div class="feedback-item">
                        <strong class="feedback-user">{{ $feedback->user->name }}</strong>
                        <span class="feedback-rating">⭐ {{ $feedback->rating }}/5</span>
                        <p class="feedback-comment">{{ $feedback->comment }}</p>

                        <div class="feedback-actions">
                            @if (Auth::id() === $feedback->user_id)
                                <a href="{{ route('feedback.edit', $feedback->id) }}" class="edit-btn">Edit</a>
                                <form action="{{ route('feedback.destroy', $feedback->id) }}" method="POST"
                                    class="inline-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="delete-btn">Delete</button>
                                </form>
                            @endif

                            <form action="{{ route('feedback.report', $feedback->id) }}" method="POST"
                                class="inline-form">
                                @csrf
                                <button type="submit" class="report-btn">Report</button>
                            </form>
                        </div>
                    </div>
                @endforeach
            @else
                <p class="no-feedback">No feedback yet. Be the first to review this course!</p>
            @endif

            <!-- Feedback Button -->
            <button id="show-feedback-form" class="feedback-btn">📝 Leave a Review</button>

            <!-- Feedback Form (Initially Hidden) -->
            <form id="feedback-form" action="{{ route('feedback.store', $course->id) }}" method="POST"
                class="feedback-form">
                @csrf
                <div class="form-group">
                    <label class="form-label">Rating:</label>
                    <select name="rating" class="form-select" required>
                        <option value="5">⭐️⭐️⭐️⭐️⭐️ (Excellent)</option>
                        <option value="4">⭐️⭐️⭐️⭐️ (Good)</option>
                        <option value="3">⭐️⭐️⭐️ (Average)</option>
                        <option value="2">⭐️⭐️ (Below Average)</option>
                        <option value="1">⭐️ (Poor)</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Comment:</label>
                    <textarea name="comment" class="form-textarea" rows="3" placeholder="Write your feedback..."></textarea>
                </div>

                <button type="submit" class="submit-btn">Submit Feedback</button>
            </form>
        </div>
    </div>

    <!-- PDF Viewer Modal -->
    <div id="pdf-viewer" class="pdf-modal">
        <div class="pdf-container">
            <button id="close-pdf-viewer" class="close-btn">✖</button>
            <h3 id="pdf-title" class="pdf-title"></h3>
            <iframe id="pdf-frame" class="pdf-frame" src=""></iframe>
        </div>
    </div>
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/course-detail.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
@endpush

@push('scripts')
    <script src="{{ asset('js/course-detail.js') }}"></script>

@endpush
