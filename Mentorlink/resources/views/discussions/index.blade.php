@extends('layouts.app', ['course' => $course])

@section('title', 'Course Discussions')

@section('content')
    <div class="container">

        <a href="{{ route('discussion.create', ['courseId' => $course->id]) }}" class="btn btn-primary">
            Create Discussion
        </a>
        <h2>Course Discussions</h2>

        @foreach ($discussions as $discussion)
            <div class="thread">
                <div class="question">
                    <img src="{{ $discussion->user->avatar ?? 'https://placehold.co/40x40' }}"
                        alt="{{ $discussion->user->name }}">
                    <strong>{{ $discussion->user->name }}:</strong>
                    {{ $discussion->message }}
                </div>

                <!-- Replies Section -->
                <div class="replies">
                    @foreach ($discussion->replies as $reply)
                        <div class="reply">
                            <img src="{{ $reply->user->avatar ?? 'https://placehold.co/40x40' }}"
                                alt="{{ $reply->user->name }}">
                            <strong>{{ $reply->user->name }}:</strong>
                            {{ $reply->message }}

                            <!-- Reply Form (Nested Reply) -->
                            <form action="{{ route('discussion.reply', $reply->id) }}" method="POST">
                                @csrf
                                <textarea name="message" placeholder="Write a reply..." rows="2"></textarea>
                                <button type="submit">Reply</button>
                            </form>
                        </div>
                    @endforeach
                </div>

                <!-- Reply Button for Main Discussion -->
                <form action="{{ route('discussion.reply', $discussion->id) }}" method="POST">
                    @csrf
                    <textarea name="message" placeholder="Write a reply..." rows="3"></textarea>
                    <button type="submit">Reply</button>
                </form>
            </div>
        @endforeach
    </div>
@endsection
