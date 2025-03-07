@extends('layouts.learner')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-3xl mx-auto">
        <h1 class="text-3xl font-bold mb-8">{{ $quiz->title }}</h1>

        <form action="{{ route('quizzes.submit', $quiz->id) }}" method="POST" class="space-y-8">
            @csrf
            
            @foreach($quiz->questions as $index => $question)
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-xl font-semibold mb-4">Question {{ $index + 1 }}</h3>
                <p class="text-gray-700 mb-4">{{ $question->content }}</p>

                <div class="space-y-3">
                    @foreach($question->options as $option)
                    <label class="flex items-start space-x-3">
                        <input type="radio" 
                               name="answers[{{ $question->id }}]" 
                               value="{{ $option->id }}"
                               class="mt-1">
                        <span class="text-gray-700">{{ $option->content }}</span>
                    </label>
                    @endforeach
                </div>
            </div>
            @endforeach

            <div class="flex justify-between items-center">
                <span class="text-gray-600">
                    Time Remaining: <span id="timer" class="font-semibold">{{ $quiz->time_limit }}:00</span>
                </span>
                <button type="submit" class="bg-blue-500 text-white px-6 py-2 rounded-md hover:bg-blue-600">
                    Submit Quiz
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    // Timer functionality
    function startTimer(duration) {
        let timer = duration * 60;
        const display = document.getElementById('timer');
        
        const countdown = setInterval(function () {
            const minutes = parseInt(timer / 60, 10);
            const seconds = parseInt(timer % 60, 10);

            display.textContent = minutes + ':' + (seconds < 10 ? '0' : '') + seconds;

            if (--timer < 0) {
                clearInterval(countdown);
                document.querySelector('form').submit();
            }
        }, 1000);
    }

    // Start the timer when the page loads
    window.onload = function () {
        startTimer({{ $quiz->time_limit }});
    }
</script>
@endpush
@endsection
