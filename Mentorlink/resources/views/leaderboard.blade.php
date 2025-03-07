@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6">
    <h1 class="text-3xl font-bold text-center mb-6">Leaderboard</h1>

    {{-- Leaderboard Table --}}
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
        <table class="w-full border-collapse">
            <thead>
                <tr class="bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200 text-left">
                    <th class="p-3">Rank</th>
                    <th class="p-3">User</th>
                    <th class="p-3 text-right">Points</th>
                </tr>
            </thead>
            <tbody>
                @foreach($leaderboard as $user)
                <tr class="border-b border-gray-300 dark:border-gray-600 {{ auth()->id() == $user['user_id'] ? 'bg-yellow-200 dark:bg-yellow-500' : 'hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                    <td class="p-3">{{ $user['rank'] }}</td>
                    <td class="p-3 flex items-center">
                        <img src="{{ asset('storage/avatars/' . ($user['avatar'] ?? 'default.png')) }}" class="w-8 h-8 rounded-full mr-3" alt="Avatar">
                        {{ $user['name'] }}
                    </td>
                    <td class="p-3 text-right">{{ number_format($user['points']) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- User's Personal Rank --}}
    @if($userRank)
    <div class="text-center mt-6 text-lg font-semibold">
        Your Rank: <span class="text-blue-600 dark:text-blue-400">{{ $userRank }}</span> out of {{ $totalUsers }} users
    </div>
    @endif
</div>
@endsection
