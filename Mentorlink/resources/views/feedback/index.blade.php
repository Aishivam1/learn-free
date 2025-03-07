@extends('layouts.learner')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold">Course Feedback</h1>
            @if(auth()->user()->isEnrolled($course))
                <a href="{{ route('feedback.create', ['course' => $course->id]) }}" 
                   class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600">
                    Add Feedback
                </a>
            @endif
        </div>

        <div class="bg-white rounded-lg shadow-sm p-6 mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-semibold mb-2">{{ $course->title }}</h2>
                    <p class="text-gray-600">{{ $course->mentor->name }}</p>
                </div>
                <div class="text-right">
                    <div class="text-3xl font-bold text-yellow-400 mb-1">{{ number_format($course->average_rating, 1) }}</div>
                    <div class="text-sm text-gray-600">{{ $course->feedback_count }} reviews</div>
                </div>
            </div>

            <div class="mt-6">
                <div class="space-y-2">
                    @for($i = 5; $i >= 1; $i--)
                        <div class="flex items-center">
                            <span class="w-12 text-sm text-gray-600">{{ $i }} star</span>
                            <div class="flex-1 h-4 mx-4 bg-gray-200 rounded-full overflow-hidden">
                                <div class="h-full bg-yellow-400" 
                                     style="width: {{ ($course->ratings_distribution[$i] ?? 0) / $course->feedback_count * 100 }}%">
                                </div>
                            </div>
                            <span class="w-12 text-sm text-gray-600">
                                {{ $course->ratings_distribution[$i] ?? 0 }}
                            </span>
                        </div>
                    @endfor
                </div>
            </div>
        </div>

        <div class="space-y-6">
            @forelse($feedback as $review)
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <div class="flex items-start justify-between">
                        <div class="flex items-start space-x-4">
                            @if(!$review->is_anonymous)
                                <div class="flex-shrink-0">
                                    <img class="h-10 w-10 rounded-full" src="{{ $review->user->avatar_url }}" alt="{{ $review->user->name }}">
                                </div>
                            @endif
                            <div>
                                <div class="flex items-center">
                                    <h3 class="font-semibold">
                                        {{ $review->is_anonymous ? 'Anonymous User' : $review->user->name }}
                                    </h3>
                                    <span class="mx-2 text-gray-300">•</span>
                                    <span class="text-gray-600 text-sm">
                                        {{ $review->created_at->diffForHumans() }}
                                    </span>
                                </div>
                                <div class="flex items-center mt-1">
                                    @for($i = 1; $i <= 5; $i++)
                                        <svg class="w-5 h-5 {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-300' }}" 
                                             fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                        </svg>
                                    @endfor
                                </div>
                            </div>
                        </div>
                        @if(auth()->id() === $review->user_id)
                            <div class="flex space-x-2">
                                <a href="{{ route('feedback.edit', $review->id) }}" 
                                   class="text-blue-600 hover:text-blue-700">Edit</a>
                                <form action="{{ route('feedback.destroy', $review->id) }}" method="POST" 
                                      onsubmit="return confirm('Are you sure you want to delete this feedback?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-700">Delete</button>
                                </form>
                            </div>
                        @endif
                    </div>
                    <p class="mt-4 text-gray-700">{{ $review->content }}</p>
                </div>
            @empty
                <div class="bg-white rounded-lg shadow-sm p-6 text-center">
                    <p class="text-gray-600">No feedback yet. Be the first to review this course!</p>
                </div>
            @endforelse

            {{ $feedback->links() }}
        </div>
    </div>
</div>
@endsection
