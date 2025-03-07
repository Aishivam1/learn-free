@extends('layouts.learner')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-3xl mx-auto">
        <h1 class="text-3xl font-bold mb-8">Start New Discussion</h1>

        <form action="{{ route('discussions.store') }}" method="POST" class="bg-white rounded-lg shadow-sm p-6">
            @csrf

            <div class="mb-6">
                <label for="course_id" class="block text-sm font-medium text-gray-700 mb-2">Select Course</label>
                <select name="course_id" id="course_id" class="w-full rounded-md border-gray-300 shadow-sm" required>
                    <option value="">Select a course...</option>
                    @foreach($courses as $course)
                        <option value="{{ $course->id }}">{{ $course->title }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-6">
                <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Discussion Title</label>
                <input type="text" name="title" id="title" 
                       class="w-full rounded-md border-gray-300 shadow-sm"
                       placeholder="What would you like to discuss?"
                       required>
            </div>

            <div class="mb-6">
                <label for="content" class="block text-sm font-medium text-gray-700 mb-2">Content</label>
                <textarea name="content" id="content" rows="6" 
                          class="w-full rounded-md border-gray-300 shadow-sm"
                          placeholder="Describe your topic in detail..."
                          required></textarea>
            </div>

            <div class="flex justify-end space-x-4">
                <a href="{{ route('discussions.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600">
                    Cancel
                </a>
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600">
                    Create Discussion
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
