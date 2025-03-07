@extends('layouts.app')

@section('content')
<div class="container">
    <h1>{{ $discussion->title }}</h1>
    <div class="forum-thread-body">
        <p>{{ $discussion->body }}</p>
    </div>
    <div class="forum-replies">
        <h2>Replies</h2>
        @foreach ($discussion->replies as $reply)
            <div class="forum-reply">
                <p>{{ $reply->body }}</p>
            </div>
        @endforeach
    </div>
    <div class="reply-form">
        <h2>Post a Reply</h2>
        <form action="{{ route('forum.store') }}" method="POST">
            @csrf
            <input type="hidden" name="parent_id" value="{{ $discussion->id }}">
            <div class="form-group">
                <label for="body">Reply</label>
                <textarea name="body" id="body" class="form-control" rows="3" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Post Reply</button>
        </form>
    </div>
</div>
@endsection