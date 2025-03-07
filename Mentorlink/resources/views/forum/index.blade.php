@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Community Forum</h1>
    <div class="forum-threads">
        @foreach ($discussions as $discussion)
            <div class="forum-thread">
                <h2>{{ $discussion->title }}</h2>
                <p>{{ $discussion->body }}</p>
                <a href="{{ route('forum.show', $discussion->id) }}">View Thread</a>
            </div>
        @endforeach
    </div>
</div>
@endsection