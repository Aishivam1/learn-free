@extends('layouts.app')

@push('styles')
    <style>
        /* General Styles */
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f5f7fa;
            color: #333;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }

        .card {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            padding: 25px;
            margin-top: 20px;
            text-align: center;
        }

        .result-image {
            display: block;
            margin: 15px auto;
            width: 120px;
            height: auto;
        }

        .score-box {
            font-size: 20px;
            font-weight: bold;
            padding: 10px;
            border-radius: 8px;
            display: inline-block;
            margin-top: 10px;
        }

        .text-green {
            color: #0056b3;
        }

        .text-red {
            color: #dc3545;
        }

        .button {
            display: inline-block;
            background-color: #007bff;
            color: white;
            font-size: 16px;
            font-weight: bold;
            padding: 10px 20px;
            border-radius: 8px;
            transition: background-color 0.3s, transform 0.2s;
            text-decoration: none;
            margin: 10px;
        }

        .button:hover {
            background-color: #0056b3;
            transform: translateY(-2px);
        }

        .grade-box {
            font-size: 18px;
            font-weight: bold;
            padding: 12px;
            border-radius: 6px;
            display: inline-block;
            margin-top: 10px;
        }

        .grade-Aplus {
            background-color: #28a745;
            color: white;
        }

        .grade-A {
            background-color: #17a2b8;
            color: white;
        }

        .grade-B {
            background-color: #ffc107;
            color: #212529;
        }

        .grade-C {
            background-color: #fd7e14;
            color: white;
        }

        .grade-D {
            background-color: #dc3545;
            color: white;
        }

        .grade-F {
            background-color: #6c757d;
            color: white;
        }
    </style>
@endpush

@section('content')
    @if ($score >= $quiz->passing_score)
        <canvas id="confetti-canvas"></canvas>
    @else
        <canvas id="sad-canvas"></canvas>
    @endif

    <div class="container">
        <div class="card">
            <h1 class="title">Quiz Results</h1>

            {{-- Display Image Based on Score --}}
            @if ($score >= $quiz->passing_score)
                <img src="{{ asset('images/pass.png') }}" alt="Passed" class="result-image">
            @else
                <img src="{{ asset('images/fail.png') }}" alt="Failed" class="result-image">
            @endif

            {{-- Score & Grade --}}
            <div class="score-box {{ $score >= $quiz->passing_score ? 'text-green' : 'text-red' }}">
                Final Score: {{ $score }}%
            </div>

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

            {{-- Action Buttons --}}
            <div class="buttons">
                @if ($score < 75)
                    <a href="{{ route('quizzes.show', ['course' => $course->id, 'quiz' => $quiz->id]) }}" class="button">
                        Retry Quiz
                    </a>
                @endif

                @if ($score >= 75 && $courseCompleted)
                    <a href="{{ route('certificate.generate', $course->id) }}" class="button">
                        Generate Certificate
                    </a>
                @endif
            </div>
        </div>
    </div>
@endsection

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
