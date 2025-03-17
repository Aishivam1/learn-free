@extends('layouts.app')

@section('title', 'Discussion Details')

@section('content')
<div class="container">
    <h2>Discussion Details</h2>

    <div class="thread">
        <div class="question">
            <img src="{{ $discussion->user->avatar ?? 'https://placehold.co/40x40' }}" alt="{{ $discussion->user->name }}">
            <strong>{{ $discussion->user->name }}:</strong>
            {{ $discussion->message }}
        </div>

        <div class="thread-footer">
            <span>💬 Replies: {{ $discussion->replies->count() }}</span>
            <form action="{{ route('discussion.like', $discussion->id) }}" method="POST">
                @csrf
                <button type="submit">👍 Like ({{ count($discussion->likes ?? []) }})</button>
            </form>
        </div>
    </div>

    <h3>Replies</h3>
    @foreach ($discussion->replies as $reply)
        <div class="reply">
            <img src="{{ $reply->user->avatar ?? 'https://placehold.co/40x40' }}" alt="{{ $reply->user->name }}">
            <strong>{{ $reply->user->name }}:</strong>
            {{ $reply->message }}
        </div>
    @endforeach

    <!-- Reply Form -->
    @include('discussions.reply-form', ['discussionId' => $discussion->id])
</div>
@endsection
