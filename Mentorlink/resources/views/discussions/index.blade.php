@extends('layouts.app')

@section('title', 'All Discussions')

@section('content')
    <div class="container">
        <div class="header-row">
            <div class="header-left">
                <h1 class="page-title">All Discussions</h1>
            </div>
            <div class="header-right">
                @if (Auth::user()->isLearner())
                    <a href="{{ route('discussions.create') }}" class="btn primary-btn">Start New Discussion</a>
                    <a href="{{ route('discussions.my') }}" class="btn outline-btn">My Discussions</a>
                @endif
                @if (Auth::user()->isAdmin())
                    <a href="{{ route('discussions.reported') }}" class="btn warning-btn">Reported Discussions</a>
                @endif
            </div>
        </div>

        <!-- Filters -->
        <div class="filter-card">
            <div class="card-body">
                <form action="{{ route('discussions.index') }}" method="GET" class="filter-form">
                    <div class="form-group">
                        <label for="course_id" class="form-label">Filter by Course</label>
                        <select name="course_id" id="course_id" class="form-select">
                            <option value="">All Courses</option>
                            @foreach ($courses as $courseOption)
                                <option value="{{ $courseOption->id }}"
                                    {{ request('course_id') == $courseOption->id ? 'selected' : '' }}>
                                    {{ $courseOption->title }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="sort" class="form-label">Sort By</label>
                        <select name="sort" id="sort" class="form-select">
                            <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Latest</option>
                            <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Oldest</option>
                        </select>
                    </div>
                    <div class="form-group button-group">
                        <button type="submit" class="btn primary-btn">Apply Filters</button>
                        <a href="{{ route('discussions.index') }}" class="btn outline-btn">Reset</a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Discussions List -->
        @if ($discussions->isEmpty())
            <div class="alert-info">
                No discussions found. {{ Auth::user()->isLearner() ? 'Start a new conversation!' : '' }}
            </div>
        @else
            <div class="discussions-list">
                @foreach ($discussions as $discussion)
                    <div class="discussion-card">
                        <div class="card-body">
                            <div class="discussion-header">
                                <div class="user-info">
                                    <img src="{{ asset('avatar/' . $discussion->user->avatar) }}" alt="Avatar"
                                           class="avatar">

                                    <div class="user-details">
                                        <h5 class="user-name">{{ $discussion->user->name }}</h5>
                                        <small class="post-meta">{{ $discussion->created_at->diffForHumans() }} in
                                            <a
                                                href="{{ route('discussions.list', $discussion->course_id) }}">{{ $discussion->course->title }}</a>
                                        </small>
                                    </div>
                                </div>
                                <div class="reply-count">
                                    <span class="badge">{{ $discussion->replies_count }}
                                        {{ Str::plural('reply', $discussion->replies_count) }}</span>
                                </div>
                            </div>

                            <p class="discussion-content">{{ Str::limit($discussion->message, 200) }}</p>

                            <div class="discussion-actions">
                                <a href="{{ route('discussions.show', $discussion->id) }}"
                                    class="btn outline-btn small-btn">View Discussion</a>

                                <div class="action-buttons">
                                    <button class="btn outline-secondary small-btn like-button"
                                        data-discussion-id="{{ $discussion->id }}">
                                        <i class="far fa-heart"></i> <span class="like-count">0</span>
                                    </button>

                                    @if (Auth::id() != $discussion->user_id)
                                        <button class="btn outline-warning small-btn report-button"
                                            data-discussion-id="{{ $discussion->id }}">
                                            <i class="far fa-flag"></i> <span class="report-status">Report</span>
                                        </button>
                                    @endif

                                    @if (Auth::id() == $discussion->user_id ||
                                            Auth::user()->isAdmin() ||
                                            (Auth::user()->isMentor() && Auth::user()->id == $discussion->course->mentor_id))
                                        <form action="{{ route('discussions.delete', $discussion->id) }}" method="POST"
                                            onsubmit="return confirm('Are you sure you want to delete this discussion?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn outline-danger small-btn">Delete</button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach

                <div class="pagination-container">
                    {{ $discussions->links() }}
                </div>
            </div>
        @endif
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Initialize like counts
                const likeButtons = document.querySelectorAll('.like-button');

                likeButtons.forEach(button => {
                    const discussionId = button.dataset.discussionId;
                    const likeCountElement = button.querySelector('.like-count');

                    // Get initial like count
                    fetch(`/discussions/${discussionId}/like-count`)
                        .then(response => response.json())
                        .then(data => {
                            likeCountElement.textContent = data.likes;
                        });

                    // Check if user has liked
                    fetch(`/discussions/${discussionId}/has-liked`)
                        .then(response => response.json())
                        .then(data => {
                            if (data.liked) {
                                button.querySelector('i').classList.remove('far');
                                button.querySelector('i').classList.add('fas');
                                button.classList.add('liked');
                            }
                        });

                    // Handle like button click
                    button.addEventListener('click', function() {
                        fetch(`/discussions/${discussionId}/like`, {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'Content-Type': 'application/json'
                                }
                            })
                            .then(response => response.json())
                            .then(data => {
                                likeCountElement.textContent = data.likeCount;

                                const icon = button.querySelector('i');
                                if (icon.classList.contains('far')) {
                                    icon.classList.remove('far');
                                    icon.classList.add('fas');
                                    button.classList.add('liked');
                                } else {
                                    icon.classList.remove('fas');
                                    icon.classList.add('far');
                                    button.classList.remove('liked');
                                }
                            });
                    });
                });

                // Report functionality
                const reportButtons = document.querySelectorAll('.report-button');

                // Check if discussions are reported by the current user
                reportButtons.forEach(button => {
                    const discussionId = button.dataset.discussionId;
                    const reportStatusElement = button.querySelector('.report-status');

                    fetch(`/discussions/${discussionId}/is-reported-by-user`)
                        .then(response => response.json())
                        .then(data => {
                            if (data.isReported) {
                                reportStatusElement.textContent = 'Reported';
                                button.classList.add('reported');
                                button.classList.remove('outline-warning');
                                button.classList.add('warning-btn');
                            }
                        });

                    // Handle report button click
                    button.addEventListener('click', function() {
                        if (confirm('Are you sure you want to report this discussion?')) {
                            fetch(`/discussions/${discussionId}/report`, {
                                    method: 'POST',
                                    headers: {
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                        'Content-Type': 'application/json'
                                    }
                                })
                                .then(response => response.json())
                                .then(data => {
                                    if (data.success) {
                                        if (data.userReported) {
                                            reportStatusElement.textContent = 'Reported';
                                            button.classList.add('reported');
                                            button.classList.remove('outline-warning');
                                            button.classList.add('warning-btn');
                                            alert('Discussion has been reported.');
                                        } else {
                                            reportStatusElement.textContent = 'Report';
                                            button.classList.remove('reported');
                                            button.classList.add('outline-warning');
                                            button.classList.remove('warning-btn');
                                            alert('Report has been removed.');
                                        }
                                    }
                                })
                                .catch(error => {
                                    console.error('Error:', error);
                                    alert('An error occurred while reporting the discussion.');
                                });
                        }
                    });
                });
            });
        </script>
    @endpush
    @push('styles')
        <style>
            /* Container and Layout */
            .container {
                max-width: 1140px;
                margin: 0 auto;
                padding: 20px;
            }

            .header-row {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 24px;
            }

            .header-left {
                flex: 0 0 66.66%;
            }

            .header-right {
                flex: 0 0 33.33%;
                text-align: right;
            }

            /* Typography */
            .page-title {
                font-size: 1.75rem;
                margin: 0;
                font-weight: 600;
            }

            /* Buttons */
            .btn {
                display: inline-block;
                font-weight: 400;
                text-align: center;
                vertical-align: middle;
                cursor: pointer;
                padding: 8px 16px;
                font-size: 14px;
                line-height: 1.5;
                border-radius: 4px;
                text-decoration: none;
                transition: all 0.3s ease;
                border: 1px solid transparent;
            }

            .primary-btn {
                background-color: #0d6efd;
                color: white;
                border-color: #0d6efd;
            }

            .primary-btn:hover {
                background-color: #0b5ed7;
                border-color: #0a58ca;
            }

            .outline-btn {
                background-color: transparent;
                color: #0067c1;
                border-color: #418ed2;
                margin-left: 8px;
            }

            .outline-btn:hover {
                background-color: #084298;
                color: white;
            }

            .warning-btn {
                background-color: #ffc107;
                color: #212529;
                border-color: #ffc107;
                margin-left: 8px;
            }

            .warning-btn:hover {
                background-color: #ffca2c;
                border-color: #ffc720;
            }

            .small-btn {
                padding: 4px 8px;
                font-size: 12px;
            }

            .outline-secondary {
                background-color: transparent;
                color: #6c757d;
                border-color: #075ca6;
                margin-right: 8px;
            }

            .outline-warning {
                background-color: transparent;
                color: #ffc107;
                border-color: #ffc107;
                margin-right: 8px;
            }

            .outline-danger {
                background-color: transparent;
                color: #dc3545;
                border-color: #dc3545;
            }

            .outline-danger:hover {
                background-color: #dc3545;
                color: white;
            }

            /* Cards */
            .filter-card {
                border: 1px solid rgba(0, 0, 0, 0.125);
                border-radius: 6px;
                margin-bottom: 24px;
                background-color: #fff;
            }

            .card-body {
                padding: 16px;
            }

            .discussion-card {
                border: 1px solid rgba(0, 0, 0, 0.125);
                border-radius: 6px;
                margin-bottom: 16px;
                background-color: #fff;
            }

            /* Form Elements */
            .filter-form {
                display: flex;
                flex-wrap: wrap;
                gap: 16px;
            }

            .form-group {
                flex: 1 0 30%;
                margin-bottom: 0;
            }

            .button-group {
                display: flex;
                align-items: flex-end;
            }

            .form-label {
                display: block;
                margin-bottom: 8px;
                font-weight: 500;
            }

            .form-select {
                display: block;
                width: 100%;
                padding: 8px 12px;
                font-size: 14px;
                font-weight: 400;
                line-height: 1.5;
                color: #212529;
                background-color: #fff;
                background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23343a40' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M2 5l6 6 6-6'/%3e%3c/svg%3e");
                background-repeat: no-repeat;
                background-position: right 8px center;
                background-size: 16px 12px;
                border: 1px solid #ced4da;
                border-radius: 4px;
                appearance: none;
            }

            /* Alerts */
            .alert-info {
                padding: 12px 16px;
                margin-bottom: 16px;
                border: 1px solid #b8daff;
                border-radius: 4px;
                background-color: #cfe2ff;
                color: #084298;
            }

            /* Discussions */
            .discussions-list {
                margin-top: 16px;
            }

            .discussion-header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 8px;
            }

            .user-info {
                display: flex;
                align-items: center;
            }

            .avatar {
                width: 40px;
                height: 40px;
                border-radius: 50%;
                margin-right: 12px;
                object-fit: cover;
            }

            .user-details {
                display: flex;
                flex-direction: column;
            }

            .user-name {
                margin: 0;
                font-size: 16px;
                font-weight: 500;
            }

            .post-meta {
                color: #ff0303;
                font-size: 12px;
            }

            .post-meta a {
                color: #0d6efd;
                text-decoration: none;
            }

            .reply-count .badge {
                display: inline-block;
                padding: 4px 8px;
                font-size: 12px;
                font-weight: 500;
                line-height: 1;
                color: #fff;
                background-color: #0d6efd;
                border-radius: 10px;
            }

            .discussion-content {
                margin-bottom: 16px;
                line-height: 1.5;
            }

            .discussion-actions {
                display: flex;
                justify-content: space-between;
                align-items: center;
            }

            .action-buttons {
                display: flex;
            }

            /* Pagination */
            .pagination-container {
                display: flex;
                justify-content: center;
                margin-top: 24px;
            }

            /* Responsive Design */
            @media (max-width: 768px) {
                .header-row {
                    flex-direction: column;
                    align-items: flex-start;
                }

                .header-right {
                    width: 100%;
                    text-align: left;
                    margin-top: 12px;
                }

                .filter-form {
                    flex-direction: column;
                    gap: 12px;
                }

                .form-group {
                    width: 100%;
                }

                .discussion-header {
                    flex-direction: column;
                    align-items: flex-start;
                }

                .reply-count {
                    margin-top: 8px;
                }

                .discussion-actions {
                    flex-direction: column;
                    align-items: flex-start;
                }

                .action-buttons {
                    margin-top: 8px;
                }
            }

            /* Additional Styling */
            .liked {
                background-color: #ffffff;
                color: rgb(217, 7, 7);
            }

            .reported {
                background-color: #ffc107;
                color: #212529;
            }
        </style>
    @endpush

@endsection
