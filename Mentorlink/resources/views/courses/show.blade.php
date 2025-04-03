@extends('layouts.app')

@section('title', $course->title . ' - Course Details')
<meta name="csrf-token" content="{{ csrf_token() }}">
@section('content')
    @if ($course->enrollments->contains('user_id', auth()->id()))
        <a href="{{ route('courses.my') }}" class="btn btn-my-course">My Courses</a>
    @else
        <a href="{{ route(request('from') === 'pending' ? 'admin.courses.pending' : 'courses.index') }}"
            class="btn btn-my-course">Back to All Courses</a>
    @endif
    <div class="container">
        <!-- Hidden input for course ID -->
        <input type="hidden" name="course_id" value="{{ $course->id }}">

        <!-- Back to Courses Button -->


        <div class="course-details">
            <h1 class="course-title">Title: {{ $course->title }}</h1>
            <h1 class="course-mentor"><strong>👨‍🏫 Mentor:</strong> {{ optional($course->mentor)->name ?? 'Unknown' }}</h1>
            <!-- #region -->
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
                    <video id="videoPlayer" class="video-player"
                        oncontextmenu="return false;"controlsList="nodownload noplaybackrate" preload="metadata">
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
                                <div id="progressBar-{{ $video->id }}" class="progress-bar" style="width: 0%;"
                                    data-video-id="{{ $video->id }}"></div>

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
            <div id="quiz-section" class="quiz-section">
                <p class="alert-message">
                    <i class="alert-icon"></i> You need to complete the course to attend the quiz.
                </p>
            </div>
        @endif

        <div class="feedback-section">
            <h3 class="feedback-title">📢 Student Feedback</h3>

            @if ($course->feedback->count() > 0)
                @foreach ($course->feedback as $feedback)
                    <div class="feedback-item">
                        <strong class="feedback-user">{{ $feedback->user->name }}</strong>
                        <span class="feedback-rating">⭐ {{ $feedback->rating }}/5</span>
                        @if ($feedback->comment)
                            <p class="feedback-comment">{{ $feedback->comment }}</p>
                        @endif

                        <div class="feedback-actions">
                            @if (Auth::id() === $feedback->user_id)
                                <button class="edit-feedback-btn" data-id="{{ $feedback->id }}"
                                    data-rating="{{ $feedback->rating }}"
                                    data-comment="{{ $feedback->comment }}">Edit</button>
                                <form action="{{ route('feedback.destroy', $feedback->id) }}" method="POST"
                                    class="inline-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="delete-btn">Delete</button>
                                </form>
                            @endif
                            {{-- 
                            <form action="{{ route('feedback.report', $feedback->id) }}" method="POST"
                                class="inline-form">
                                @csrf
                                <button type="submit" class="report-btn">Report</button>
                            </form> --}}
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
            <form id="edit-feedback-form" action="" method="GET" class="feedback-form" style="display: none;">
                @csrf
                @method('PUT')
                <input type="hidden" name="feedback_id" id="edit-feedback-id">

                <div class="form-group">
                    <label class="form-label">Rating:</label>
                    <select name="rating" id="edit-feedback-rating" class="form-select" required>
                        <option value="5">⭐️⭐️⭐️⭐️⭐️ (Excellent)</option>
                        <option value="4">⭐️⭐️⭐️⭐️ (Good)</option>
                        <option value="3">⭐️⭐️⭐️ (Average)</option>
                        <option value="2">⭐️⭐️ (Below Average)</option>
                        <option value="1">⭐️ (Poor)</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Comment:</label>
                    <textarea name="comment" id="edit-feedback-comment" class="form-textarea" rows="3"></textarea>
                </div>

                <button type="submit" class="submit-btn">Update Feedback</button>
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* Main Layout */
        .container {
            width: 90%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem 0;
        }

        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f5f7fa;
            color: #333;
            line-height: 1.6;
        }

        /* Course Details Section */
        .course-details {
            text-align: center;
            padding: 2rem 1rem;
            margin-bottom: 2rem;
        }

        .course-title,
        .course-mentor {
            font-size: 2rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
        }

        .course-description {
            font-size: 1.1rem;
            color: #666;
            margin-bottom: 1rem;
        }

        .course-difficulty {
            font-size: 1rem;
            margin-bottom: 1.5rem;
        }

        .difficulty-badge {
            padding: 0.3rem 1rem;
            border-radius: 999px;
            color: #000;
            font-weight: bold;
        }

        .difficulty-beginner {
            background-color: #4ade80;
        }

        .difficulty-intermediate {
            background-color: #facc15;
        }

        .difficulty-advanced {
            background-color: #f87171;
        }

        /* Button Styles */
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
        }

        .btn-my-course:hover {
            background-color: #0056b3;
        }

        .back-btn-container {
            display: flex;
            justify-content: flex-start;
            margin-bottom: 20px;
        }

        /* Materials List */
        .materials-list {
            margin-top: 2.5rem;
        }

        .materials-title,
        .feedback-title {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 1.5rem;
            text-align: center;
        }

        .section-title {
            font-size: 1.25rem;
            font-weight: 600;
            margin: 1.5rem 0 1rem;
            text-align: center;
        }

        .materials-items {
            list-style-type: none;
            padding: 0;
            margin: 0;
        }

        .material-item {
            background-color: #fff;
            padding: 1rem;
            border-radius: 0.5rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-bottom: 1rem;
        }

        .material-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.5rem;
        }

        .no-content,
        .no-feedback {
            text-align: center;
            color: #6b7280;
            font-style: italic;
        }

        /* Video Player */
        .video-container {
            position: relative;
            background-color: #fff;
            padding: 1.5rem;
            border-radius: 0.5rem;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin: 2rem 0;
            display: none;
        }

        .video-title {
            font-size: 1.25rem;
            font-weight: 600;
            text-align: center;
            margin-bottom: 1rem;
        }

        .custom-video-wrapper {
            position: relative;
            width: 100%;
            max-height: 500px;
        }

        .video-player {
            width: 100%;
            max-height: 500px;
            border-radius: 0.25rem;
            background-color: #000;
        }

        /* Custom Video Controls */
        .custom-controls {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 0.75rem;
            margin-top: 1rem;
        }

        .control-btn {
            background-color: #3b82f6;
            color: white;
            border: none;
            padding: 0.5rem 0.75rem;
            font-size: 1rem;
            border-radius: 0.25rem;
            cursor: pointer;
            transition: background-color 0.2s ease;
        }

        .control-btn:hover {
            background-color: #2563eb;
        }

        .volume-slider {
            width: 6rem;
        }

        /* Progress Bars */
        .progress-container {
            width: 100%;
            height: 0.625rem;
            background: #e5e7eb;
            border-radius: 0.25rem;
            margin-top: 0.75rem;
            overflow: hidden;
        }

        .progress-bar {
            height: 5px;
            background: #3b82f6;
            transition: width 0.5s ease;
        }

        /* Action Buttons */
        .play-btn,
        .download-btn,
        .view-pdf-btn,
        .quiz-btn,
        .feedback-btn,
        .submit-btn {
            background-color: #3b82f6;
            color: white;
            padding: 0.35rem 0.75rem;
            border-radius: 0.25rem;
            text-decoration: none;
            font-weight: 500;
            font-size: 0.875rem;
            cursor: pointer;
            border: none;
            transition: background-color 0.2s ease;
        }

        .play-btn:hover,
        .download-btn:hover,
        .view-pdf-btn:hover,
        .quiz-btn:hover,
        .feedback-btn:hover,
        .submit-btn:hover {
            background-color: #2563eb;
        }

        .quiz-btn,
        .feedback-btn,
        .submit-btn {
            padding: 0.6rem 1.25rem;
            border-radius: 0.5rem;
            font-weight: 600;
        }

        .feedback-btn {
            display: block;
            margin: 1rem 0;
        }

        .pdf-actions {
            display: flex;
            gap: 0.5rem;
        }

        /* Close Buttons */
        .close-btn {
            position: absolute;
            top: 0.625rem;
            right: 0.625rem;
            background-color: rgba(239, 68, 68, 0.8);
            color: white;
            border: none;
            border-radius: 50%;
            width: 1.875rem;
            height: 1.875rem;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 1rem;
            z-index: 10;
            transition: background-color 0.2s ease;
        }

        .close-btn:hover {
            background-color: rgb(239, 68, 68);
        }

        /* PDF Viewer */
        .pdf-modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.75);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 50;
        }

        .pdf-container {
            position: relative;
            background-color: white;
            padding: 1.25rem;
            border-radius: 0.5rem;
            width: 80%;
            max-width: 56.25rem;
            max-height: 80vh;
        }

        .pdf-title {
            font-size: 1.125rem;
            font-weight: 600;
            text-align: center;
            margin-bottom: 1rem;
        }

        .pdf-frame {
            width: 100%;
            height: 70vh;
            border: none;
        }

        /* Quiz Section */
        .quiz-section {
            text-align: center;
            margin: 2rem 0;
        }

        .alert-message {
            color: #dc2626;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .alert-icon::before {
            content: "⚠️";
            font-size: 1.25rem;
            animation: pulse 1.5s infinite;
        }

        @keyframes pulse {
            0% {
                opacity: 0.7;
            }

            50% {
                opacity: 1;
            }

            100% {
                opacity: 0.7;
            }
        }

        /* Feedback Section */
        .feedback-section {
            margin: 3rem 0;
        }

        .feedback-item {
            border: 1px solid #e5e7eb;
            border-radius: 0.5rem;
            padding: 1rem;
            margin-bottom: 1rem;
            background-color: #f9fafb;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }

        .feedback-user {
            font-size: 1.125rem;
            display: block;
            margin-bottom: 0.25rem;
        }

        .feedback-rating {
            color: #f59e0b;
            font-weight: 600;
            display: inline-block;
            margin-bottom: 0.5rem;
        }

        .feedback-comment {
            color: #4b5563;
            margin-bottom: 0.75rem;
        }

        .feedback-actions {
            display: flex;
            gap: 0.75rem;
        }

        .delete-btn {
            background-color: #dc2626;
            color: white;
            height: 30px;
            width: 50px;
            border-radius: 5px;
            border: none;
        }

        .delete-btn:hover {
            background-color: white;
            color: #dc2626;
        }

        .edit-feedback-btn {
            background-color: #3b82f6;
            color: white;
            height: 30px;
            width: 50px;
            border-radius: 5px;
            border: none;
        }

        .edit-feedback-btn:hover {
            background-color: white;
            color: #3b82f6;
        }

        /* Feedback Form */
        .feedback-form {
            border: 1px solid #e5e7eb;
            border-radius: 0.5rem;
            padding: 1.25rem;
            margin-top: 1rem;
            background-color: white;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            display: none;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
        }

        .form-select,
        .form-textarea {
            width: 100%;
            padding: 0.5rem;
            border: 1px solid #d1d5db;
            border-radius: 0.375rem;
            font-size: 1rem;
        }

        .form-textarea {
            resize: vertical;
            min-height: 6rem;
        }

        .inline-form {
            display: inline;
        }

        /* Responsive Styles */
        @media (max-width: 768px) {
            .container {
                width: 95%;
                padding: 1rem 0;
            }

            .course-title,
            .course-mentor {
                font-size: 1.5rem;
            }

            .material-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.5rem;
            }

            .pdf-actions {
                margin-top: 0.5rem;
            }

            .pdf-container {
                width: 95%;
                padding: 1rem;
            }

            .custom-controls {
                flex-wrap: wrap;
            }
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize feedback form toggle
            initFeedbackForm();

            // Initialize PDF viewer
            initPdfViewer();

            // Initialize video player and controls
            initVideoPlayer();
        });

        /**
         * Initialize feedback form toggle functionality
         */
        function initFeedbackForm() {
            const showFeedbackFormBtn = document.getElementById('show-feedback-form');
            const feedbackForm = document.getElementById('feedback-form');

            if (showFeedbackFormBtn && feedbackForm) {
                showFeedbackFormBtn.addEventListener('click', function() {
                    feedbackForm.style.display = (feedbackForm.style.display === 'none' || feedbackForm.style
                        .display === '') ? 'block' : 'none';
                });
            }
        }

        /**
         * Initialize PDF viewer functionality
         */
        function initPdfViewer() {
            const pdfButtons = document.querySelectorAll('.view-pdf-btn');
            const pdfFrame = document.getElementById('pdf-frame');
            const pdfViewer = document.getElementById('pdf-viewer');
            const pdfTitle = document.getElementById('pdf-title');
            const closePdfViewer = document.getElementById('close-pdf-viewer');

            // PDF viewer event handlers
            if (pdfButtons.length > 0 && pdfFrame && pdfViewer && pdfTitle && closePdfViewer) {
                // Handle PDF View Click
                pdfButtons.forEach(button => {
                    button.addEventListener('click', function() {
                        const pdfUrl = this.getAttribute('data-pdf');
                        const pdfName = this.getAttribute('data-name');

                        pdfTitle.textContent = pdfName;
                        pdfFrame.src = pdfUrl; // Fetch and display the PDF
                        pdfViewer.style.display = 'flex'; // Show using display flex
                    });
                });

                // Close PDF Viewer
                closePdfViewer.addEventListener('click', function() {
                    pdfViewer.style.display = 'none'; // Hide using display none
                    pdfFrame.src = ""; // Reset PDF
                    pdfTitle.textContent = ""; // Clear PDF name after closing
                });

                // Also close PDF viewer when clicking outside the PDF container
                pdfViewer.addEventListener('click', function(e) {
                    if (e.target === pdfViewer) {
                        pdfViewer.style.display = 'none'; // Hide using display none
                        pdfFrame.src = "";
                        pdfTitle.textContent = "";
                    }
                });
            }
        }

        /**
         * Initialize video player and controls functionality
         */
        function initVideoPlayer() {
            const videoButtons = document.querySelectorAll('.play-btn');
            const videoPlayer = document.getElementById('videoPlayer');
            const videoSource = document.getElementById('videoSource');
            const videoContainer = document.getElementById('video-container');
            const videoTitle = document.getElementById('current-video-title');
            const closeVideoBtn = document.getElementById('close-video-btn');
            const videoIdInput = document.getElementById('videoId');

            // Get CSRF token for AJAX requests
            const csrfTokenMeta = document.querySelector('meta[name="csrf-token"]');
            const csrfToken = csrfTokenMeta ? csrfTokenMeta.getAttribute('content') : null;

            // Get course ID from hidden input
            const courseIdInput = document.querySelector('input[name="course_id"]');
            const courseId = courseIdInput ? courseIdInput.value : null;

            let currentVideoId = null;
            let lastUpdate = 0; // Prevent frequent updates

            // Custom controls
            const playPauseBtn = document.getElementById('playPauseBtn');
            const volumeSlider = document.getElementById('volumeSlider');
            const fullscreenBtn = document.getElementById('fullscreenBtn');
            const activeProgressBar = document.getElementById('activeProgressBar');

            // Setup custom video controls if elements exist
            setupVideoControls(videoPlayer, playPauseBtn, volumeSlider, fullscreenBtn);

            // Close video button handler
            setupCloseVideoButton(closeVideoBtn, videoContainer, videoPlayer, videoSource, videoTitle);

            // Add click event listeners to all play buttons
            setupPlayButtons(
                videoButtons,
                videoPlayer,
                videoSource,
                videoContainer,
                videoTitle,
                activeProgressBar,
                playPauseBtn,
                videoIdInput
            );

            // Setup progress tracking
            if (videoPlayer) {
                videoPlayer.addEventListener('timeupdate', function() {
                    updateVideoProgress(
                        videoPlayer,
                        activeProgressBar,
                        csrfToken,
                        courseId,
                        lastUpdate,
                        videoIdInput
                    );
                });

                // Video Ended Event
                videoPlayer.addEventListener('ended', function() {
                    handleVideoEnded(
                        videoPlayer,
                        playPauseBtn,
                        videoContainer,
                        videoSource,
                        videoTitle,
                        videoIdInput
                    );
                });
            }
        }

        /**
         * Setup video controls (play/pause, volume, fullscreen)
         */
        function setupVideoControls(videoPlayer, playPauseBtn, volumeSlider, fullscreenBtn) {
            if (!videoPlayer) return;

            // Play/Pause Button
            if (playPauseBtn) {
                playPauseBtn.addEventListener('click', function() {
                    if (videoPlayer.paused) {
                        videoPlayer.play()
                            .then(() => {
                                playPauseBtn.textContent = '⏸';
                            })
                            .catch(error => {
                                console.error("Error playing video:", error);
                            });
                    } else {
                        videoPlayer.pause();
                        playPauseBtn.textContent = '▶️';
                    }
                });
            }

            // Volume Control
            if (volumeSlider) {
                volumeSlider.addEventListener('input', function() {
                    videoPlayer.volume = this.value;
                });
            }

            // Fullscreen Button
            if (fullscreenBtn) {
                fullscreenBtn.addEventListener('click', function() {
                    if (videoPlayer.requestFullscreen) {
                        videoPlayer.requestFullscreen();
                    } else if (videoPlayer.mozRequestFullScreen) { // Firefox
                        videoPlayer.mozRequestFullScreen();
                    } else if (videoPlayer.webkitRequestFullscreen) { // Chrome, Safari, Edge
                        videoPlayer.webkitRequestFullscreen();
                    } else if (videoPlayer.msRequestFullscreen) { // IE/Edge
                        videoPlayer.msRequestFullscreen();
                    }
                });
            }
        }

        /**
         * Setup close video button functionality
         */
        function setupCloseVideoButton(closeVideoBtn, videoContainer, videoPlayer, videoSource, videoTitle) {
            if (closeVideoBtn && videoContainer && videoPlayer) {
                closeVideoBtn.addEventListener('click', function() {
                    videoPlayer.pause();
                    videoContainer.style.display = 'none';
                    if (videoSource) {
                        videoSource.src = "";
                        videoPlayer.load();
                    }
                    if (videoTitle) {
                        videoTitle.textContent = "";
                    }
                });
            }
        }

        /**
         * Setup play buttons for videos
         */
        function setupPlayButtons(
            videoButtons,
            videoPlayer,
            videoSource,
            videoContainer,
            videoTitle,
            activeProgressBar,
            playPauseBtn,
            videoIdInput
        ) {
            if (videoButtons.length === 0 || !videoPlayer || !videoContainer) return;

            videoButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const videoUrl = this.getAttribute('data-video');
                    const videoId = this.getAttribute('data-id');
                    const title = this.getAttribute('data-title');

                    // Set video title
                    if (videoTitle) videoTitle.textContent = title;

                    // Store video ID in hidden input
                    if (videoIdInput) videoIdInput.value = videoId;

                    // Set video source
                    if (videoSource) {
                        videoSource.src = videoUrl;
                        videoPlayer.load();
                    }

                    // Show video container and scroll to it
                    videoContainer.style.display = 'block';
                    videoContainer.scrollIntoView({
                        behavior: 'smooth'
                    });

                    // Get stored progress for this video
                    const progressBar = document.getElementById(`progressBar-${videoId}`);
                    const storedProgress = progressBar ? parseInt(progressBar.style.width) : 0;

                    // Update active progress bar to match stored progress
                    if (activeProgressBar) {
                        activeProgressBar.style.width = (storedProgress || 0) + "%";
                    }

                    // Try to play the video after a short delay
                    setTimeout(() => {
                        // If there's significant progress, ask if user wants to resume
                        if (storedProgress > 10 && storedProgress < 95) {
                            if (confirm(`Resume from ${storedProgress}% progress?`)) {
                                // Calculate time position based on progress percentage
                                videoPlayer.addEventListener('loadedmetadata',
                                    function onceLoaded() {
                                        const resumeTime = (storedProgress / 100) * videoPlayer
                                            .duration;
                                        videoPlayer.currentTime = resumeTime;
                                        videoPlayer.removeEventListener('loadedmetadata',
                                            onceLoaded);
                                    });
                            }
                        }

                        videoPlayer.play()
                            .then(() => {
                                if (playPauseBtn) playPauseBtn.textContent = '⏸';
                            })
                            .catch(error => {
                                console.error("Error playing video:", error);
                                if (playPauseBtn) playPauseBtn.textContent = '▶️';
                            });
                    }, 300);
                });
            });
        }

        /**
         * Update video progress and sync with server
         */
        function updateVideoProgress(videoPlayer, activeProgressBar, csrfToken, courseId, lastUpdate, videoIdInput) {
            if (!videoPlayer || !videoPlayer.duration) return;

            // Calculate progress percentage
            const progress = Math.round((videoPlayer.currentTime / videoPlayer.duration) * 100);

            // Update active progress bar
            if (activeProgressBar) {
                activeProgressBar.style.width = progress + "%";
            }

            // Get the current video ID from the hidden input
            const videoId = videoIdInput ? videoIdInput.value : null;

            // Update individual video progress bar
            if (videoId) {
                const progressBar = document.getElementById(`progressBar-${videoId}`);
                if (progressBar) {
                    progressBar.style.width = progress + "%";
                }
            }

            // Only send update to server if required and enough time has passed (throttling)
            if (csrfToken && videoId && courseId && (Date.now() - lastUpdate > 5000 || progress >= 99)) {
                window.lastUpdate = Date.now();

                // Construct the URL for the material progress update
                const progressUrl = `/materials/${videoId}/progress`;

                // Send progress update to server via POST request
                fetch(progressUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify({
                            material_id: videoId, // The video ID
                            course_id: courseId, // Associated course
                            progress: progress // Current progress percentage
                        })
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(data => {
                        console.log("Material progress updated:", data);
                        // If progress is 100, check quiz availability (to display the quiz button)
                        if (progress >= 99) {
                            checkQuizAvailability();
                        }
                    })
                    .catch(error => console.error("Error updating material progress:", error));
            }
        }

        /**
         * Handle video ended event
         */
        function handleVideoEnded(videoPlayer, playPauseBtn, videoContainer, videoSource, videoTitle, videoIdInput) {
            console.log("Video ended");

            // Get the current video ID from hidden input
            const videoId = videoIdInput ? videoIdInput.value : null;

            // Update progress to 100% when video ends
            if (videoId) {
                const progressBar = document.getElementById(`progressBar-${videoId}`);
                if (progressBar) {
                    progressBar.style.width = "100%";
                }

                // Get CSRF token for final progress update
                const csrfTokenMeta = document.querySelector('meta[name="csrf-token"]');
                const csrfToken = csrfTokenMeta ? csrfTokenMeta.getAttribute('content') : null;

                // Get course ID for final progress update
                const courseIdInput = document.querySelector('input[name="course_id"]');
                const courseId = courseIdInput ? courseIdInput.value : null;

                // Send final 100% progress update
                if (csrfToken && courseId) {
                    const progressUrl = `/materials/${videoId}/progress`;

                    fetch(progressUrl, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken
                            },
                            body: JSON.stringify({
                                material_id: videoId,
                                course_id: courseId,
                                progress: 100
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            console.log("Final progress updated:", data);
                            // Directly call checkQuizAvailability() since progress is 100%
                            checkQuizAvailability();
                        })
                        .catch(error => console.error("Error updating final progress:", error));
                }
            }

            // Reset play button
            if (playPauseBtn) playPauseBtn.textContent = '▶️';

            // Reset video source
            if (videoSource) {
                videoSource.src = "";
                videoPlayer.load();
            }

            // Reset video title
            if (videoTitle) {
                videoTitle.textContent = "";
            }

            // Clear video ID
            if (videoIdInput) {
                videoIdInput.value = "";
            }
        }

        function checkQuizAvailability() {
            console.log("Checking quiz availability...");
            const courseIdInput = document.querySelector('input[name="course_id"]');
            const courseId = courseIdInput ? courseIdInput.value : null;

            if (!courseId) {
                console.error("Course ID not found");
                return;
            }

            fetch(`/courses/${courseId}/enrollment-status`, {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    console.log("Enrollment status:", data);
                    const quizSection = document.getElementById('quiz-section');
                    if (!quizSection) {
                        console.error("Quiz section element not found.");
                        return;
                    }
                    // Update quiz section based on enrollment status and quiz attempts
                    if (data.progress >= 100) {
                        if (data.quizAttempts < 3) {
                            quizSection.innerHTML =
                                `<a href="{{ route('quiz.attempt', $course->id) }}" class="quiz-btn">📝 Attend Quiz</a>`;
                        } else {
                            quizSection.innerHTML = `<p class="alert-message">
                    <i class="alert-icon"></i> You have reached the maximum quiz attempts (3).
                </p>`;
                        }
                    } else {
                        quizSection.innerHTML = `<p class="alert-message">
                <i class="alert-icon"></i> You need to complete the course to attend the quiz.
            </p>`;
                    }
                })
                .catch(error => console.error("Error checking quiz availability:", error));
        }
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.edit-feedback-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const feedbackId = this.getAttribute('data-id');
                    const rating = this.getAttribute('data-rating');
                    const comment = this.getAttribute('data-comment');

                    // Populate the form with existing data
                    document.getElementById('edit-feedback-id').value = feedbackId;
                    document.getElementById('edit-feedback-rating').value = rating;
                    document.getElementById('edit-feedback-comment').value = comment;

                    // Set the form action dynamically
                    document.getElementById('edit-feedback-form').action =
                        `/feedback/${feedbackId}`;

                    // Show the edit form
                    document.getElementById('edit-feedback-form').style.display = 'block';
                });
            });
        });
    </script>
@endpush
