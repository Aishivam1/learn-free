@extends('layouts.app')

@section('title', 'Attempt Quiz - ' . $course->title)

@push('styles')
<style>
    /* Main Layout */
    .container {
        width: 90%;
        max-width: 1200px;
        margin: 0 auto;
        padding: 2rem 0;
    }

    body {
        font-family: 'Roboto', sans-serif;
        background-color: #f5f7fa;
        color: #333;
        line-height: 1.6;
    }

    /* Quiz Card Styles */
    .quiz-card {
        background-color: #fff;
        padding: 1.5rem;
        border-radius: 0.5rem;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        margin-bottom: 1.5rem;
        border-left: 5px solid #007bff;
        display: none; /* Hide all questions initially */
    }

    .quiz-question {
        font-size: 1.25rem;
        font-weight: 600;
        margin-bottom: 1rem;
        color: #007bff;
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
        cursor: pointer;
    }

    /* Timer */
    #timer {
        text-align: center;
        font-size: 1.5rem;
        margin-bottom: 1rem;
        font-weight: bold;
        color: #007bff;
    }

    /* Form and Button Styles */
    form {
        margin-top: 2rem;
    }

    .submit-btn {
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
        margin: 2rem auto 0;
    }

    .submit-btn:hover {
        background-color: #0056b3;
    }

    .alert-message {
        color: #dc2626;
        font-weight: 600;
        text-align: center;
        margin: 1rem 0;
    }
</style>
@endpush

@section('content')
    <div class="container">
        <h1 class="course-title">📝 Quiz for {{ $course->title }}</h1>

        <!-- Timer Display -->
        <div id="timer">
            Time remaining: <span id="time">45</span> seconds
        </div>

        <!-- Quiz Form -->
        <form id="quiz-form" action="{{ route('quiz.submit', $course->id) }}" method="POST">
            @csrf

            @foreach ($quizzes as $quiz)
                <div class="quiz-card" id="question-{{ $loop->index }}">
                    <p class="quiz-question">{{ $loop->iteration }}. {{ $quiz->question }}</p>
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
                </div>
            @endforeach

            <button type="submit" class="submit-btn" id="submit-btn" style="display: none;">Submit Quiz</button>
        </form>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        let questions = document.querySelectorAll('.quiz-card');
        let currentQuestionIndex = 0;
        let timeLeft = 45;
        const timerElement = document.getElementById('time');
        const submitBtn = document.getElementById('submit-btn');
        const quizForm = document.getElementById('quiz-form');

        function showQuestion(index) {
            questions.forEach((q, i) => {
                q.style.display = i === index ? 'block' : 'none';
            });
            timeLeft = 45; // Reset timer for each question
            updateTimer();
        }

        function updateTimer() {
            timerElement.textContent = timeLeft;
        }

        function nextQuestion() {
            if (currentQuestionIndex < questions.length - 1) {
                currentQuestionIndex++;
                showQuestion(currentQuestionIndex);
                startTimer();
            } else {
                submitBtn.style.display = 'block';
                quizForm.submit(); // Auto-submit if it's the last question
            }
        }

        function startTimer() {
            const countdownInterval = setInterval(function() {
                timeLeft--;
                updateTimer();

                if (timeLeft <= 0) {
                    clearInterval(countdownInterval);
                    nextQuestion();
                }
            }, 1000);
        }

        showQuestion(currentQuestionIndex);
        startTimer();
    });
</script>
@endpush
