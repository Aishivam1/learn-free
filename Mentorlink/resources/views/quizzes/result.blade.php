@extends('layouts.learner')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-3xl mx-auto">
        <div class="bg-white rounded-lg shadow-lg p-8">
            <h1 class="text-3xl font-bold mb-6">Quiz Results</h1>

            <div class="mb-8">
                <div class="flex items-center justify-between mb-4">
                    <span class="text-xl">Final Score:</span>
                    <span class="text-2xl font-bold {{ $score >= $quiz->passing_score ? 'text-green-600' : 'text-red-600' }}">
                        {{ $score }}%
                    </span>
                </div>

                @if($score >= $quiz->passing_score)
                    <div class="bg-green-100 text-green-700 p-4 rounded-md">
                        Congratulations! You've passed the quiz.
                    </div>
                @else
                    <div class="bg-red-100 text-red-700 p-4 rounded-md">
                        Unfortunately, you didn't reach the passing score. Keep studying and try again!
                    </div>
                @endif
            </div>

            <div class="space-y-8">
                @foreach($answers as $index => $answer)
                <div class="border-t pt-6">
                    <h3 class="text-lg font-semibold mb-3">Question {{ $index + 1 }}</h3>
                    <p class="text-gray-700 mb-4">{{ $answer->question->content }}</p>

                    <div class="space-y-3">
                        @foreach($answer->question->options as $option)
                            <div class="flex items-start space-x-3">
                                <div class="w-6 h-6 flex items-center justify-center">
                                    @if($option->id === $answer->selected_option_id)
                                        @if($option->is_correct)
                                            <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                        @else
                                            <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                        @endif
                                    @endif
                                </div>
                                <span class="text-gray-700 {{ $option->is_correct ? 'font-semibold' : '' }}">
                                    {{ $option->content }}
                                </span>
                            </div>
                        @endforeach
                    </div>

                    @if(!$answer->is_correct && $answer->question->explanation)
                        <div class="mt-4 bg-blue-50 p-4 rounded-md">
                            <h4 class="font-semibold text-blue-800 mb-2">Explanation:</h4>
                            <p class="text-blue-700">{{ $answer->question->explanation }}</p>
                        </div>
                    @endif
                </div>
                @endforeach
            </div>

            <div class="mt-8 flex justify-between">
                <a href="{{ route('modules.show', $quiz->module_id) }}" class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600">
                    Back to Module
                </a>
                @if($score < $quiz->passing_score)
                    <a href="{{ route('quizzes.show', $quiz->id) }}" class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600">
                        Retry Quiz
                    </a>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
