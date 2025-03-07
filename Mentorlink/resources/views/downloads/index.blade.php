@extends('layouts.learner')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-3xl font-bold mb-8">Course Materials</h1>

        @forelse($downloads->groupBy('module_id') as $moduleId => $moduleDownloads)
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <h2 class="text-xl font-semibold mb-4">
                    {{ $moduleDownloads->first()->module->title }}
                </h2>

                <div class="space-y-4">
                    @foreach($moduleDownloads as $download)
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                            <div class="flex items-center space-x-4">
                                <div class="flex-shrink-0">
                                    @switch($download->file_type)
                                        @case('pdf')
                                            <svg class="w-8 h-8 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a2 2 0 01-2 2H6a2 2 0 01-2-2V4z"/>
                                            </svg>
                                            @break
                                        @case('doc')
                                            <svg class="w-8 h-8 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a2 2 0 01-2 2H6a2 2 0 01-2-2V4z"/>
                                            </svg>
                                            @break
                                        @default
                                            <svg class="w-8 h-8 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a2 2 0 01-2 2H6a2 2 0 01-2-2V4z"/>
                                            </svg>
                                    @endswitch
                                </div>
                                <div>
                                    <h3 class="font-medium">{{ $download->title }}</h3>
                                    <p class="text-sm text-gray-600">
                                        {{ $download->description }}
                                    </p>
                                </div>
                            </div>
                            <a href="{{ route('downloads.download', $download->id) }}" 
                               class="flex items-center space-x-2 text-blue-600 hover:text-blue-700">
                                <span>Download</span>
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                          d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                </svg>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        @empty
            <div class="bg-white rounded-lg shadow-sm p-6 text-center">
                <p class="text-gray-600">No downloadable materials available.</p>
            </div>
        @endforelse
    </div>
</div>
@endsection
