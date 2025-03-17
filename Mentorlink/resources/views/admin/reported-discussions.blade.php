@extends('layouts.app')

@section('title', 'Reported Discussions')

@section('content')
<div class="container">
    <h2>Reported Discussions</h2>

    @foreach ($reportedDiscussions as $discussion)
        <div class="thread">
            <div class="question">
                <img src="{{ $discussion->user->avatar ?? 'https://placehold.co/40x40' }}" alt="{{ $discussion->user->name }}">
                <strong>{{ $discussion->user->name }}:</strong>
                {{ $discussion->message }}
            </div>

            <h4>Reports:</h4>
            <ul>
                @foreach ($discussion->reports as $report)
                    <li><strong>User ID:</strong> {{ $report['user_id'] }} | <strong>Reason:</strong> {{ $report['reason'] }}</li>
                @endforeach
            </ul>

            <form action="{{ route('discussion.dismissReports', $discussion->id) }}" method="POST">
                @csrf
                <button type="submit" class="btn-warning">Dismiss Reports</button>
            </form>

            <form action="{{ route('discussion.delete', $discussion->id) }}" method="POST">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn-danger">Delete Discussion</button>
            </form>
        </div>
    @endforeach
</div>
@endsection
