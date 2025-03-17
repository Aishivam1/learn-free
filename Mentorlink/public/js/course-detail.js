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
            feedbackForm.style.display = (feedbackForm.style.display === 'none' || feedbackForm.style.display === '') ? 'block' : 'none';
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
        playPauseBtn
    );

    // Setup progress tracking
    if (videoPlayer) {
        videoPlayer.addEventListener('timeupdate', function() {
            updateVideoProgress(
                videoPlayer,
                activeProgressBar,
                currentVideoId,
                csrfToken,
                courseId,
                lastUpdate
            );
        });

        // Video Ended Event
        videoPlayer.addEventListener('ended', function() {
            handleVideoEnded(
                videoPlayer,
                playPauseBtn,
                videoContainer,
                videoSource,
                videoTitle
            );
            currentVideoId = null;
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
    playPauseBtn
) {
    if (videoButtons.length === 0 || !videoPlayer || !videoContainer) return;

    videoButtons.forEach(button => {
        button.addEventListener('click', function() {
            const videoUrl = this.getAttribute('data-video');
            const videoId = this.getAttribute('data-id');
            const title = this.getAttribute('data-title');

            // Set video title
            if (videoTitle) videoTitle.textContent = title;

            // Set video source
            if (videoSource) {
                videoSource.src = videoUrl;
                videoPlayer.load();
            }

            // Show video container and scroll to it
            videoContainer.style.display = 'block';
            videoContainer.scrollIntoView({ behavior: 'smooth' });

            // Update current video ID
            window.currentVideoId = videoId;

            // Reset the progress bar
            if (activeProgressBar) {
                activeProgressBar.style.width = "0%";
            }

            // Try to play the video after a short delay
            setTimeout(() => {
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
function updateVideoProgress(videoPlayer, activeProgressBar, currentVideoId, csrfToken, courseId, lastUpdate) {
    if (!videoPlayer || !videoPlayer.duration) return;

    // Calculate progress percentage
    const progress = Math.round((videoPlayer.currentTime / videoPlayer.duration) * 100);

    // Update active progress bar
    if (activeProgressBar) {
        activeProgressBar.style.width = progress + "%";
    }

    // Update individual video progress bar
    const videoId = window.currentVideoId;
    if (videoId) {
        const progressBar = document.getElementById(`progressBar-${videoId}`);
        if (progressBar) {
            progressBar.style.width = progress + "%";
        }
    }

    // Only send update to server if we have the necessary information
    // and enough time has passed since last update (throttle requests)
    if (csrfToken && videoId && courseId && Date.now() - lastUpdate > 5000) {
        window.lastUpdate = Date.now();

        // Construct the URL for the progress update
        const progressUrl = `/courses/${courseId}/progress`;

        // Send progress update to server
        fetch(progressUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({
                video_id: videoId,
                course_id: courseId,
                progress: progress
            })
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => console.log("Progress updated:", data))
        .catch(error => console.error("Error updating progress:", error));
    }
}

/**
 * Handle video ended event
 */
function handleVideoEnded(videoPlayer, playPauseBtn, videoContainer, videoSource, videoTitle) {
    console.log("Video ended");

    // Reset play button
    if (playPauseBtn) playPauseBtn.textContent = '▶️';

    // Hide the video container
    if (videoContainer) {
        videoContainer.style.display = 'none';
    }

    // Reset video source
    if (videoSource) {
        videoSource.src = "";
        videoPlayer.load();
    }

    // Reset video title
    if (videoTitle) {
        videoTitle.textContent = "";
    }
}