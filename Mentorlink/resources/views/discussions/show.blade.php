@extends('layouts.app')

@section('title', 'Discussion Details')

@section('content')
    <div class="container py-4">
        <div class="row mb-4">
            <div class="col-md-4 text-md-end">
                <a href="{{ route('discussions.list', $discussion->course_id) }}" class="btn btn-outline-primary">
                    <i class="fas fa-arrow-left"></i> Back to Course Discussions
                </a>
            </div>
        </div>

        <!-- Main Discussion -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Discussion by {{ $discussion->user->name }}</h5>
                <small>{{ $discussion->created_at->format('M d, Y h:i A') }}</small>
            </div>
            <div class="card-body">
                <div class="d-flex mb-3">
                    <img src="{{ $discussion->user->avatar ? asset('avatar/' . $discussion->user->avatar) : asset('avatar/default.png') }}"
                        alt="Avatar" class="rounded-circle me-3" style="width: 50px; height: 50px;">
                        <div>
                        <h5>{{ $discussion->user->name }}</h5>
                        <p class="text-muted mb-0">
                            <small>Posted in <a
                                    href="{{ route('discussions.list', $discussion->course_id) }}">{{ $discussion->course->title }}</a></small>
                        </p>
                    </div>
                </div>
                <p class="card-text">{{ $discussion->message }}</p>

                <div class="d-flex justify-content-between align-items-center mt-4">
                    <div class="d-flex align-items-center">
                        <button class="btn btn-sm btn-outline-primary me-2 like-button"
                            data-discussion-id="{{ $discussion->id }}">
                            <i class="far fa-heart"></i> <span class="like-count">0</span>
                        </button>
                        <button class="btn btn-sm btn-outline-warning me-2 report-button"
                            data-discussion-id="{{ $discussion->id }}">
                            <i class="far fa-flag"></i> <span class="report-text">Report</span>
                        </button>
                    </div>

                    @if (Auth::id() == $discussion->user_id ||
                            Auth::user()->isAdmin() ||
                            (Auth::user()->isMentor() && Auth::user()->id == $discussion->course->mentor_id))
                        <form action="{{ route('discussions.delete', $discussion->id) }}" method="POST"
                            onsubmit="return confirm('Are you sure you want to delete this discussion?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                        </form>
                    @endif
                </div>
            </div>
        </div>

        <!-- Replies Section -->
        <h4 class="mb-3">{{ $discussion->replies->count() }} {{ Str::plural('Reply', $discussion->replies->count()) }}
        </h4>

        @if ($discussion->replies->isEmpty())
            <div class="alert alert-info">
                No replies yet. Be the first to reply!
            </div>
        @else
            @foreach ($discussion->replies as $reply)
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="d-flex mb-3">
                            <img src="{{ $reply->user->avatar ?? asset('images/default-avatar.png') }}" alt="Avatar"
                                class="rounded-circle me-3" style="width: 40px; height: 40px;">
                            <div>
                                <h6 class="mb-0">{{ $reply->user->name }}</h6>
                                <small class="text-muted">{{ $reply->created_at->diffForHumans() }}</small>
                            </div>
                        </div>
                        <p class="card-text">{{ $reply->message }}</p>

                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div class="d-flex align-items-center">
                                <button class="btn btn-sm btn-outline-primary me-2 like-button"
                                    data-discussion-id="{{ $reply->id }}">
                                    <i class="far fa-heart"></i> <span class="like-count">0</span>
                                </button>
                                <button class="btn btn-sm btn-outline-warning report-button"
                                    data-discussion-id="{{ $reply->id }}">
                                    <i class="far fa-flag"></i> <span class="report-text">Report</span>
                                </button>
                            </div>

                            @if (Auth::id() == $reply->user_id ||
                                    Auth::user()->isAdmin() ||
                                    (Auth::user()->isMentor() && Auth::user()->id == $discussion->course->mentor_id))
                                <form action="{{ route('discussions.delete', $reply->id) }}" method="POST"
                                    onsubmit="return confirm('Are you sure you want to delete this reply?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        @endif

        <!-- Reply Form -->
        <div class="card mt-4">
            <div class="card-header bg-light">
                <h5 class="mb-0">Post a Reply</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('discussions.reply', $discussion->id) }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <textarea name="message" rows="3" class="form-control @error('message') is-invalid @enderror"
                            placeholder="Write your reply here..." required>{{ old('message') }}</textarea>
                        @error('message')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <button type="submit" class="btn btn-primary">Post Reply</button>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Initialize like and report buttons
                initializeInteractionButtons('.like-button', 'like');
                initializeInteractionButtons('.report-button', 'report');

                function initializeInteractionButtons(selector, action) {
                    const buttons = document.querySelectorAll(selector);

                    buttons.forEach(button => {
                        const discussionId = button.dataset.discussionId;

                        if (action === 'like') {
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
                                        button.classList.add('active');
                                    }
                                });
                        }

                        // Handle button click
                        button.addEventListener('click', function() {
                            fetch(`/discussions/${discussionId}/${action}`, {
                                    method: 'POST',
                                    headers: {
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                        'Content-Type': 'application/json'
                                    }
                                })
                                .then(response => response.json())
                                .then(data => {
                                    const icon = button.querySelector('i');

                                    if (action === 'like') {
                                        const likeCountElement = button.querySelector(
                                        '.like-count');
                                        likeCountElement.textContent = data.likeCount;

                                        if (icon.classList.contains('far')) {
                                            icon.classList.remove('far');
                                            icon.classList.add('fas');
                                            button.classList.add('active');
                                        } else {
                                            icon.classList.remove('fas');
                                            icon.classList.add('far');
                                            button.classList.remove('active');
                                        }
                                    } else if (action === 'report') {
                                        const reportText = button.querySelector('.report-text');

                                        if (data.userReported) {
                                            icon.classList.remove('far');
                                            icon.classList.add('fas');
                                            button.classList.add('active');
                                            reportText.textContent = 'Reported';
                                        } else {
                                            icon.classList.remove('fas');
                                            icon.classList.add('far');
                                            button.classList.remove('active');
                                            reportText.textContent = 'Report';
                                        }
                                    }
                                });
                        });
                    });
                }
            });
        </script>
    @endpush

    <style>
       .text-md-end {
         text-align: left !important; 
    }
        .like-button.active {
            color: #dc3545;
            border-color: #dc3545;
        }

        .report-button.active {
            color: #fd7e14;
            border-color: #fd7e14;
        }
    </style>
@endsection
