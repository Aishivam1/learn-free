@extends('layouts.learner')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-3xl font-bold mb-8">Notifications</h1>

        <div class="bg-white rounded-lg shadow-sm divide-y">
            @forelse($notifications as $notification)
                <div class="p-6 {{ $notification->read_at ? 'bg-white' : 'bg-blue-50' }}">
                    <div class="flex items-start justify-between">
                        <div class="flex items-start space-x-4">
                            <div class="flex-shrink-0">
                                @switch($notification->type)
                                    @case('App\Notifications\CourseEnrollment')
                                        <div class="w-10 h-10 bg-green-100 text-green-500 rounded-full flex items-center justify-center">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                      d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                        </div>
                                        @break
                                    @case('App\Notifications\NewDiscussionReply')
                                        <div class="w-10 h-10 bg-blue-100 text-blue-500 rounded-full flex items-center justify-center">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                      d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                                            </svg>
                                        </div>
                                        @break
                                    @default
                                        <div class="w-10 h-10 bg-gray-100 text-gray-500 rounded-full flex items-center justify-center">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                      d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                            </svg>
                                        </div>
                                @endswitch
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-900">
                                    {{ $notification->data['message'] }}
                                </p>
                                <p class="mt-1 text-sm text-gray-500">
                                    {{ $notification->created_at->diffForHumans() }}
                                </p>
                                @if(isset($notification->data['action_url']))
                                    <a href="{{ $notification->data['action_url'] }}" 
                                       class="mt-2 inline-flex items-center text-sm text-blue-600 hover:text-blue-700">
                                        {{ $notification->data['action_text'] ?? 'View' }}
                                        <svg class="ml-1.5 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                        </svg>
                                    </a>
                                @endif
                            </div>
                        </div>
                        @unless($notification->read_at)
                            <form action="{{ route('notifications.mark-as-read', $notification->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="text-sm text-gray-600 hover:text-gray-900">
                                    Mark as read
                                </button>
                            </form>
                        @endunless
                    </div>
                </div>
            @empty
                <div class="p-6 text-center">
                    <p class="text-gray-600">No notifications to display.</p>
                </div>
            @endforelse
        </div>

        @if($notifications->isNotEmpty())
            <div class="mt-4 flex justify-end">
                <form action="{{ route('notifications.mark-all-as-read') }}" method="POST">
                    @csrf
                    <button type="submit" class="text-sm text-gray-600 hover:text-gray-900">
                        Mark all as read
                    </button>
                </form>
            </div>
        @endif

        {{ $notifications->links() }}
    </div>
</div>
@endsection
