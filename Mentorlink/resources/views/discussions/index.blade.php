@extends('layouts.learner')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold">Course Discussions</h1>
        <a href="{{ route('discussions.create') }}" class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600">
            New Discussion
        </a>
    </div>

    <div class="space-y-6">
        @forelse($discussions as $discussion)
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-start justify-between">
                    <div>
                        <h3 class="text-xl font-semibold mb-2">
                            <a href="{{ route('discussions.show', $discussion->id) }}" class="hover:text-blue-600">
                                {{ $discussion->title }}
                            </a>
                        </h3>
                        <p class="text-gray-600 text-sm mb-4">
                            Started by {{ $discussion->user->name }} • {{ $discussion->created_at->diffForHumans() }}
                        </p>
                    </div>
                    <span class="px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-sm">
                        {{ $discussion->replies_count }} replies
                    </span>
                </div>

                <p class="text-gray-700 mb-4">{{ Str::limit($discussion->content, 200) }}</p>

                <div class="flex items-center space-x-4 text-sm">
                    <span class="text-gray-600">
                        Course: {{ $discussion->course->title }}
                    </span>
                    @if($discussion->last_reply)
                        <span class="text-gray-600">
                            Last reply: {{ $discussion->last_reply->created_at->diffForHumans() }}
                        </span>
                    @endif
                </div>
            </div>
        @empty
            <div class="bg-white rounded-lg shadow-sm p-6 text-center">
                <p class="text-gray-600">No discussions yet. Start a new discussion!</p>
            </div>
        @endforelse

        {{ $discussions->links() }}
    </div>
</div>
@endsection
