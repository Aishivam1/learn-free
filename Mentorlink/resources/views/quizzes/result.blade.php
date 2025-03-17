@extends('layouts.app')

@push('styles')
    <style>
        /* General styles */
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f5f7fa;
            color: #333;
        }

        .container {
            max-width: 900px;
            margin: 0 auto;
            padding: 20px;
        }

        .bg-white {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-top: 20px;
        }

        h1 {
            font-size: 28px;
            margin-bottom: 10px;
            text-align: center;
        }

        .result-image {
            display: block;
            margin: 10px auto;
            width: 150px;
            height: auto;
        }

        .text-green-600 {
            color: #d6dfe9;
        }

        .text-red-600 {
            color: red;
        }

        .rounded-md {
            border-radius: 6px;
            padding: 10px;
            margin-top: 10px;
        }

        .btn {
            display: inline-block;
            background-color: #007bff;
            color: white;
            font-size: 16px;
            font-weight: bold;
            padding: 10px 15px;
            border-radius: 8px;
            transition: background-color 0.3s, transform 0.2s;
            text-align: center;
            text-decoration: none;
        }

        .btn:hover {
            background-color: #0056b3;
            transform: translateY(-2px);
        }

        .gh {
            background-color: #0056b3;
            color: white;
            align-items: center;
            justify-content: center;
            display: flex;
        }
    </style>
@endpush

@section('content')
    {{-- Confetti Canvas for Celebration --}}
    @if ($score >= $quiz->passing_score)
        <canvas id="confetti-canvas"></canvas>
    @endif

    {{-- Sad Falling Emojis if Failed --}}
    @if ($score < $quiz->passing_score)
        <canvas id="sad-canvas"></canvas>
    @endif

    <div class="container mx-auto px-4 py-8">
        <div class="max-w-3xl mx-auto">
            <div class="bg-white rounded-lg shadow-lg p-8">
                <h1 class="text-3xl font-bold mb-6">Quiz Results</h1>

                {{-- Display Image Based on Score --}}
                @if ($score >= $quiz->passing_score)
                    <img src="{{ asset('images/pass.png') }}" alt="Passed" class="result-image">
                @else
                    <img src="{{ asset('images/fail.png') }}" alt="Failed" class="result-image">
                @endif

                {{-- Score & Grade --}}
                <div class="mb-8">
                    <div class="gh">
                        <span class="text-xl">Final Score:</span>
                        <span
                            class="text-2xl font-bold {{ $score >= $quiz->passing_score ? 'text-green-600' : 'text-red-600' }}">
                            {{ $score }}%
                        </span>
                    </div>

                    {{-- Determine Grade Dynamically --}}
                    @php
                        if ($score >= 90) {
                            $grade = 'A+';
                            $gradeClass = 'grade-Aplus';
                            $message = "🎉 Excellent! You're a top performer!";
                        } elseif ($score >= 80) {
                            $grade = 'A';
                            $gradeClass = 'grade-A';
                            $message = '👍 Very Good! Keep up the great work!';
                        } elseif ($score >= 70) {
                            $grade = 'B';
                            $gradeClass = 'grade-B';
                            $message = '✅ Good effort! Keep pushing.';
                        } elseif ($score >= 60) {
                            $grade = 'C';
                            $gradeClass = 'grade-C';
                            $message = '💡 Fair try! You can improve.';
                        } elseif ($score >= 50) {
                            $grade = 'D';
                            $gradeClass = 'grade-D';
                            $message = '📚 Needs improvement. Study more.';
                        } else {
                            $grade = 'F';
                            $gradeClass = 'grade-F';
                            $message = "❌ Failed. Don't give up!";
                        }
                    @endphp

                    {{-- Display Grade --}}
                    <div class="grade-box {{ $gradeClass }}">
                        Grade: {{ $grade }} <br>
                        {{ $message }}
                    </div>
                </div>

                {{-- Retake Quiz Button for Failing Users --}}
                <div class="mt-8 flex justify-between">
                    @if ($score < $quiz->passing_score)
                        <a href="{{ route('quizzes.show', $quiz->id) }}" class="btn">
                            Retry Quiz
                        </a>
                    @endif

                    {{-- Generate Certificate Button (Only if Passed and Course Completed) --}}
                    @if ($score >= $quiz->passing_score && $courseCompleted)
                        <a href="{{ route('certificate.generate', $course->id) }}" class="btn btn-primary">
                            Generate Certificate
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

{{-- Confetti Animation --}}
@push('scripts')
    @if ($score >= $quiz->passing_score)
        <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.5.1"></script>
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                const duration = 5000;
                const end = Date.now() + duration;

                (function frame() {
                    confetti({
                        particleCount: 3,
                        spread: 120,
                        origin: {
                            y: 0.6
                        }
                    });

                    if (Date.now() < end) {
                        requestAnimationFrame(frame);
                    }
                })();
            });
        </script>
    @endif

    @if ($score < $quiz->passing_score)
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                const canvas = document.getElementById("sad-canvas");
                const ctx = canvas.getContext("2d");

                canvas.width = window.innerWidth;
                canvas.height = window.innerHeight;

                const emojis = ["😢", "💔", "😭"];
                const particles = [];

                class Particle {
                    constructor(x, y, emoji) {
                        this.x = x;
                        this.y = y;
                        this.emoji = emoji;
                        this.speed = Math.random() * 2 + 1;
                    }

                    update() {
                        this.y += this.speed;
                    }

                    draw() {
                        ctx.font = "30px Arial";
                        ctx.fillText(this.emoji, this.x, this.y);
                    }
                }

                function createParticles() {
                    for (let i = 0; i < 15; i++) {
                        const x = Math.random() * canvas.width;
                        const y = Math.random() * -canvas.height;
                        const emoji = emojis[Math.floor(Math.random() * emojis.length)];
                        particles.push(new Particle(x, y, emoji));
                    }
                }

                function animate() {
                    ctx.clearRect(0, 0, canvas.width, canvas.height);
                    particles.forEach((particle) => {
                        particle.update();
                        particle.draw();
                    });
                    requestAnimationFrame(animate);
                }

                createParticles();
                animate();
            });
        </script>
    @endif
@endpush
