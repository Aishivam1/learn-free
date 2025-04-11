@extends('layouts.app')

@section('title', 'Attempt Quiz - ' . $course->title)

@push('styles')
<style>
    /* Main Layout */

    
    /* Quiz Card */
    .quiz-card {
        background-color: #fff;
        padding: 1.5rem;
        border-radius: 0.5rem;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        margin-bottom: 1.5rem;
        display: none; /* Hide all questions initially */
    }

    .quiz-question {
        font-size: 1.25rem;
        font-weight: 600;
        margin-bottom: 1rem;
    }

    .quiz-options {
        margin-top: 1rem;
    }

    .quiz-option {
        margin-bottom: 0.75rem;
        display: flex;
        align-items: center;
    }

    .quiz-option input {
        margin-right: 0.5rem;
    }

    .quiz-option label {
        font-size: 1rem;
    }

    /* Buttons */
    .btn {
        background-color: #007bff;
        color: white;
        border: none;
        padding: 0.6rem 1.25rem;
        border-radius: 0.5rem;
        font-weight: 600;
        cursor: pointer;
        transition: background-color 0.2s ease;
        display: block;
        width: 100%;
        max-width: 300px;
        margin: 1rem auto 0;
        text-align: center;
    }

    .btn:hover {
        background-color: #0056b3;
    }

    .alert-message {
        color: #dc2626;
        font-weight: 600;
        text-align: center;
        margin: 1rem 0;
    }

    /* Timer */
    #timer {
        text-align: center;
        font-size: 1.5rem;
        font-weight: bold;
        margin-bottom: 1rem;
    }

    /* Fix the "Next" Button CSS */
    .next-btn {
        background-color: #007bff !important;
        color: white !important;
        border: none;
        padding: 0.6rem 1.25rem;
        border-radius: 0.5rem;
        font-weight: 600;
        cursor: pointer;
        transition: background-color 0.2s ease;
        display: block;
        width: 100%;
        max-width: 300px;
        margin: 1rem auto 0;
        text-align: center;
    }

</style>
@endpush

@section('content')
    <div class="container">
        <h1 class="course-title">📝 Quiz for {{ $course->title }}</h1>

        <!-- Timer Display -->
        <div id="timer">Time remaining: 45 seconds</div>

        <!-- Quiz Form -->
        <form id="quiz-form" action="{{ route('quiz.submit', $course->id) }}" method="POST">
            @csrf

            @foreach ($quizzes as $index => $quiz)
                <div class="quiz-card" id="question-{{ $index }}">
                    <p class="quiz-question">{{ $index + 1 }}. {{ $quiz->question }}</p>
                    @php
                        $options = json_decode($quiz->options, true);
                    @endphp
                    <div class="quiz-options">
                        @foreach ($options as $option)
                            <div class="quiz-option">
                                <input type="radio" id="quiz-{{ $quiz->id }}-{{ Str::slug($option) }}"
                                    name="answers[{{ $quiz->id }}]" value="{{ $option }}">
                                <label for="quiz-{{ $quiz->id }}-{{ Str::slug($option) }}">{{ $option }}</label>
                            </div>
                        @endforeach
                    </div>

                    <button type="button" class="btn next-btn" data-index="{{ $index }}">Next</button>
                </div>
            @endforeach

            <button type="submit" id="submit-btn" class="btn" style="display: none;">Submit Quiz</button>
        </form>
    </div>

@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let currentQuestion = 0;
            let timeLeft = 45; // 45 seconds per question
            const timerElement = document.getElementById('timer');
            const totalQuestions = {{ count($quizzes) }};
            const quizCards = document.querySelectorAll('.quiz-card');
            const nextButtons = document.querySelectorAll('.next-btn');
            const quizForm = document.getElementById('quiz-form');
            const submitButton = document.getElementById('submit-btn');

            // Show first question
            quizCards[currentQuestion].style.display = 'block';

            function startTimer() {
                timeLeft = 45;
                timerElement.textContent = `Time remaining: ${timeLeft} seconds`;

                let countdown = setInterval(function() {
                    timeLeft--;
                    timerElement.textContent = `Time remaining: ${timeLeft} seconds`;

                    if (timeLeft <= 0) {
                        clearInterval(countdown);
                        goToNextQuestion();
                    }
                }, 1000);

                return countdown;
            }

            let countdown = startTimer();

            function goToNextQuestion() {
                clearInterval(countdown); // Stop the timer

                // Hide current question
                quizCards[currentQuestion].style.display = 'none';

                if (currentQuestion + 1 < totalQuestions) {
                    currentQuestion++;
                    quizCards[currentQuestion].style.display = 'block';
                    countdown = startTimer();
                } else {
                    // If it's the last question, submit the form
                    submitButton.style.display = 'block';
                    timerElement.textContent = "Time's up! Submit your quiz.";
                }
            }

            // Next button click event
            nextButtons.forEach((btn, index) => {
                btn.addEventListener('click', function() {
                    goToNextQuestion();
                });
            });
        });
    </script>
@endpush
