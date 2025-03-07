@extends('layouts.learner')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Discussion Thread -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-8">
            <div class="flex items-start space-x-4">
                <div class="flex-shrink-0">
                    <img class="h-12 w-12 rounded-full" src="{{ $discussion->user->avatar_url }}" alt="{{ $discussion->user->name }}">
                </div>
                <div class="flex-grow">
                    <div class="flex items-center justify-between">
                        <h1 class="text-2xl font-bold">{{ $discussion->title }}</h1>
                        <span class="text-gray-600 text-sm">{{ $discussion->created_at->diffForHumans() }}</span>
                    </div>
                    <p class="text-gray-600 text-sm mb-4">
                        Posted by {{ $discussion->user->name }} in {{ $discussion->course->title }}
                    </p>
                    <div class="prose max-w-none">
                        {!! $discussion->content !!}
                    </div>
                </div>
            </div>
        </div>

        <!-- Replies -->
        <div class="space-y-6">
            @foreach($discussion->replies as $reply)
                <div class="bg-white rounded-lg shadow-sm p-6 {{ $reply->is_solution ? 'border-2 border-green-500' : '' }}">
                    <div class="flex items-start space-x-4">
                        <div class="flex-shrink-0">
                            <img class="h-10 w-10 rounded-full" src="{{ $reply->user->avatar_url }}" alt="{{ $reply->user->name }}">
                        </div>
                        <div class="flex-grow">
                            <div class="flex items-center justify-between mb-2">
                                <div>
                                    <span class="font-semibold">{{ $reply->user->name }}</span>
                                    <span class="text-gray-600 text-sm">• {{ $reply->created_at->diffForHumans() }}</span>
                                </div>
                                @if($reply->is_solution)
                                    <span class="bg-green-100 text-green-800 px-2 py-1 rounded-full text-sm">
                                        Solution
                                    </span>
                                @endif
                            </div>
                            <div class="prose max-w-none">
                                {!! $reply->content !!}
                            </div>
                            
                            @if(auth()->id() === $discussion->user_id && !$discussion->has_solution)
                                <form action="{{ route('discussions.mark-solution', $reply->id) }}" method="POST" class="mt-4">
                                    @csrf
                                    <button type="submit" class="text-green-600 hover:text-green-700 text-sm">
                                        Mark as Solution
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Reply Form -->
        <div class="mt-8 bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-semibold mb-4">Post a Reply</h3>
            <form action="{{ route('discussions.reply', $discussion->id) }}" method="POST">
                @csrf
                <div class="mb-4">
                    <textarea name="content" rows="4" class="w-full rounded-md border-gray-300 shadow-sm" 
                              placeholder="Write your reply here..."></textarea>
                </div>
                <div class="flex justify-end">
                    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600">
                        Post Reply
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
