@extends('layouts.app')

@section('title', 'My Discussions')

@section('content')
<div class="container py-4">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h3 mb-0">My Discussions</h1>
        </div>
        <div class="col-md-4 text-md-end">
            <a href="{{ route('discussions.create') }}" class="btn btn-primary">Start New Discussion</a>
            <a href="{{ route('discussions.index') }}" class="btn btn-outline-primary ms-2">All Discussions</a>
        </div>
    </div>

    <!-- Discussions List -->
    @if($discussions->isEmpty())
        <div class="alert alert-info">
            You haven't started any discussions yet. <a href="{{ route('discussions.create') }}">Start your first discussion</a> now!
        </div>
    @else
        <div class="discussions-list">
            @foreach($discussions as $discussion)
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div>
                                <h5 class="card-title mb-0">Discussion in {{ $discussion->course->title }}</h5>
                                <small class="text-muted">Posted {{ $discussion->created_at->diffForHumans() }}</small>
                            </div>
                            <div>
                                <span class="badge bg-primary">{{ $discussion->replies_count }} {{ Str::plural('reply', $discussion->replies_count) }}</span>
                            </div>
                        </div>
                        
                        <p class="card-text">{{ Str::limit($discussion->message, 200) }}</p>
                        
                        <div class="d-flex justify-content-between align-items-center">
                            <a href="{{ route('discussions.show', $discussion->id) }}" class="btn btn-sm btn-outline-primary">View Discussion</a>
                            
                            <div class="d-flex">
                                <button 
                                    class="btn btn-sm btn-outline-secondary me-2 like-button" 
                                    data-discussion-id="{{ $discussion->id }}"
                                >
                                    <i class="far fa-heart"></i> <span class="like-count">0</span>
                                </button>
                                
                                <form action="{{ route('discussions.delete', $discussion->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this discussion?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
            
            <div class="d-flex justify-content-center mt-4">
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
    });
</script>
@endpush

<style>
    .like-button.liked {
        color: #dc3545;
        border-color: #dc3545;
    }
    .discussions-list .card:hover {
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        transform: translateY(-2px);
        transition: all 0.3s ease;
    }
</style>
@endsection