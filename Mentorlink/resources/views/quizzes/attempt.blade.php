@extends('layouts.app')

@section('title', 'Attempt Quiz - ' . $course->title)

@section('content')
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold text-center">📝 Quiz for {{ $course->title }}</h1>

        <!-- Display Quiz Questions -->
        <form action="{{ route('quiz.submit', $course->id) }}" method="POST" class="mt-6">
            @csrf

            @foreach ($quizzes as $quiz)
                <div class="mb-6 p-4 bg-white shadow-md rounded-md">
                    <p class="text-lg font-semibold mb-2">{{ $loop->iteration }}. {{ $quiz->question }}</p>

                    @php
                        $options = json_decode($quiz->options, true);
                    @endphp

                    @foreach ($options as $option)
                        <div class="flex items-center mb-2">
                            <input type="radio" name="answers[{{ $quiz->id }}]" value="{{ $option }}">
                            <label class="ml-2">{{ $option }}</label>
                        </div>
                    @endforeach

                </div>
            @endforeach

            <button type="submit" class="btn btn-primary w-full">Submit Quiz</button>
        </form>
    </div>
@endsection
