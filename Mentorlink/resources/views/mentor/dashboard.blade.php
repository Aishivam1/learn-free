@extends('layouts.mentor')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Statistics Overview -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-gray-500 text-sm font-medium">Total Courses</h3>
            <p class="text-3xl font-bold">{{ $statistics['total_courses'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-gray-500 text-sm font-medium">Total Students</h3>
            <p class="text-3xl font-bold">{{ $statistics['total_students'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-gray-500 text-sm font-medium">Average Rating</h3>
            <p class="text-3xl font-bold">{{ number_format($statistics['average_rating'], 1) }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-gray-500 text-sm font-medium">Total Modules</h3>
            <p class="text-3xl font-bold">{{ $statistics['total_modules'] }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Courses Overview -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-semibold">Your Courses</h2>
                <a href="{{ route('mentor.courses.create') }}" 
                   class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600">
                    Create Course
                </a>
            </div>
            
            <div class="space-y-4">
                @forelse($courses as $course)
                    <div class="border-b pb-4">
                        <div class="flex justify-between items-start">
                            <div>
                                <h3 class="font-medium">{{ $course->title }}</h3>
                                <p class="text-sm text-gray-600">
                                    {{ $course->enrollments_count }} students enrolled
                                    • {{ $course->modules_count }} modules
                                </p>
                            </div>
                            <a href="{{ route('mentor.courses.edit', $course) }}" 
                               class="text-blue-600 hover:text-blue-700">
                                Edit
                            </a>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500 text-center py-4">
                        You haven't created any courses yet.
                    </p>
                @endforelse
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="space-y-8">
            <!-- Recent Enrollments -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-xl font-semibold mb-6">Recent Enrollments</h2>
                <div class="space-y-4">
                    @forelse($recentEnrollments as $enrollment)
                        <div class="flex items-center space-x-4">
                            <img class="h-10 w-10 rounded-full" 
                                 src="{{ $enrollment->user->avatar_url }}" 
                                 alt="{{ $enrollment->user->name }}">
                            <div>
                                <p class="font-medium">{{ $enrollment->user->name }}</p>
                                <p class="text-sm text-gray-600">
                                    Enrolled in {{ $enrollment->course->title }}
                                    • {{ $enrollment->created_at->diffForHumans() }}
                                </p>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500 text-center py-4">
                            No recent enrollments.
                        </p>
                    @endforelse
                </div>
            </div>

            <!-- Recent Feedback -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-xl font-semibold mb-6">Recent Feedback</h2>
                <div class="space-y-4">
                    @forelse($recentFeedback as $feedback)
                        <div class="border-b pb-4">
                            <div class="flex items-start justify-between">
                                <div>
                                    <div class="flex items-center">
                                        @for($i = 1; $i <= 5; $i++)
                                            <svg class="w-5 h-5 {{ $i <= $feedback->rating ? 'text-yellow-400' : 'text-gray-300' }}" 
                                                 fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                            </svg>
                                        @endfor
                                    </div>
                                    <p class="mt-2">{{ $feedback->content }}</p>
                                    <p class="text-sm text-gray-600 mt-1">
                                        {{ $feedback->user->name }} 
                                        • {{ $feedback->course->title }}
                                        • {{ $feedback->created_at->diffForHumans() }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500 text-center py-4">
                            No feedback received yet.
                        </p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
