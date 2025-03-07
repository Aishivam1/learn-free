@extends('layouts.learner')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-3xl mx-auto">
        <h1 class="text-3xl font-bold mb-8">Edit Feedback</h1>

        <form action="{{ route('feedback.update', $feedback->id) }}" method="POST" class="bg-white rounded-lg shadow-sm p-6">
            @csrf
            @method('PUT')

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Course</label>
                <p class="text-gray-600">{{ $feedback->course->title }}</p>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Rating</label>
                <div class="flex items-center space-x-2">
                    @for($i = 1; $i <= 5; $i++)
                        <label class="cursor-pointer">
                            <input type="radio" name="rating" value="{{ $i }}" 
                                   class="hidden peer" 
                                   {{ $feedback->rating == $i ? 'checked' : '' }} 
                                   required>
                            <svg class="w-8 h-8 text-gray-300 peer-checked:text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                        </label>
                    @endfor
                </div>
            </div>

            <div class="mb-6">
                <label for="content" class="block text-sm font-medium text-gray-700 mb-2">Your Feedback</label>
                <textarea name="content" id="content" rows="6" 
                          class="w-full rounded-md border-gray-300 shadow-sm"
                          required>{{ $feedback->content }}</textarea>
            </div>

            <div class="mb-6">
                <label class="flex items-center">
                    <input type="checkbox" name="is_anonymous" 
                           class="rounded border-gray-300 text-blue-600 shadow-sm"
                           {{ $feedback->is_anonymous ? 'checked' : '' }}>
                    <span class="ml-2 text-sm text-gray-600">Submit anonymously</span>
                </label>
            </div>

            <div class="flex justify-end space-x-4">
                <a href="{{ route('courses.show', $feedback->course->id) }}" class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600">
                    Cancel
                </a>
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600">
                    Update Feedback
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
