@extends('layouts.learner')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold">{{ $module->title }}</h1>
        <p class="text-gray-600">Part of course: {{ $module->course->title }}</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Content -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-sm p-6">
                {!! $module->content !!}
                
                @if($module->video_url)
                <div class="mt-6">
                    <h3 class="text-lg font-semibold mb-4">Video Content</h3>
                    <div class="aspect-w-16 aspect-h-9">
                        <iframe src="{{ $module->video_url }}" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                    </div>
                </div>
                @endif
            </div>

            @if($module->quiz)
            <div class="mt-8">
                <h3 class="text-xl font-semibold mb-4">Module Quiz</h3>
                <a href="{{ route('quizzes.show', $module->quiz->id) }}" class="inline-block bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600">
                    Take Quiz
                </a>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold mb-4">Module Progress</h3>
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <span>Completion Status:</span>
                        <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-sm">
                            {{ $module->isCompleted() ? 'Completed' : 'In Progress' }}
                        </span>
                    </div>
                    
                    @if($module->estimated_time)
                    <div class="flex items-center justify-between">
                        <span>Estimated Time:</span>
                        <span>{{ $module->estimated_time }} minutes</span>
                    </div>
                    @endif
                </div>

                <div class="mt-6">
                    <h4 class="font-semibold mb-2">Downloads</h4>
                    @forelse($module->downloads as $download)
                        <a href="{{ route('downloads.show', $download->id) }}" class="block text-blue-500 hover:underline mb-2">
                            {{ $download->title }}
                        </a>
                    @empty
                        <p class="text-gray-500">No downloads available</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
