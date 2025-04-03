@extends('layouts.app')

@section('title', $course->title . ' - Create Quiz')

@section('content')
    <div class="container">
        <!-- Hidden input for course ID -->
        <input type="hidden" name="course_id" value="{{ $course->id }}">

        <!-- Back to Courses Button -->
        @if ($course->enrollments->contains('user_id', auth()->id()))
            <a href="{{ route('courses.my') }}" class="btn btn-my-course">My Courses</a>
        @else
            <a href="{{ route(request('from') === 'pending' ? 'admin.courses.pending' : 'courses.index') }}"
                class="btn btn-my-course">Back to All Courses</a>
        @endif

        <div class="course-details">
            <h1>Title: {{ $course->title }}</h1>
            <h1><strong>👨‍🏫 Mentor:</strong> {{ $course->mentor->name }}</h1>
            <p class="course-description">Description: {{ $course->description }}</p>
            <p class="course-difficulty"><strong>📊 Difficulty:</strong>
                <span class="difficulty-badge {{ $course->difficulty === 'beginner' ? 'beginner' : ($course->difficulty === 'intermediate' ? 'intermediate' : 'advanced') }}">
                    {{ ucfirst($course->difficulty) }}
                </span>
            </p>

            <!-- Create Quiz Form Section -->
            <div class="quiz-creation-section">
                <h2>📝 Create Quiz Question</h2>

                <!-- Explanation Text -->
                <div class="alert-info">
                    <p class="alert-title">Add Questions One By One</p>
                    <p>Fill out this form to add a question to your quiz. You can add as many questions as needed by
                        submitting this form multiple times.</p>
                </div>

                <form action="{{ route('quiz.store', $course->id) }}" method="POST" class="quiz-form">
                    @csrf

                    <!-- Question -->
                    <div class="form-group">
                        <label for="question">Question:</label>
                        <input type="text" name="question" id="question" required
                            placeholder="Enter your question">
                        @error('question')
                            <p class="error-message">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Options Section -->
                    <div class="form-group">
                        <label>Options:</label>
                        <p class="help-text">Select the correct answer by clicking the radio button.</p>

                        <div id="options-container">
                            <!-- Option 1 -->
                            <div class="option-item">
                                <input type="radio" name="correct_answer" id="option0" value="" required>
                                <div class="option-input-container">
                                    <input type="text" name="options[]" required
                                        placeholder="Option 1"
                                        oninput="document.getElementById('option0').value = this.value">
                                </div>
                            </div>

                            <!-- Option 2 -->
                            <div class="option-item">
                                <input type="radio" name="correct_answer" id="option1" value="" required>
                                <div class="option-input-container">
                                    <input type="text" name="options[]" required
                                        placeholder="Option 2"
                                        oninput="document.getElementById('option1').value = this.value">
                                </div>
                            </div>
                        </div>

                        <div class="option-controls">
                            <button type="button" id="add-option-btn" class="add-option-btn">
                                + Add Another Option
                            </button>
                            <span class="option-counter" id="option-counter">2/4 options</span>
                        </div>

                        @error('options')
                            <p class="error-message">{{ $message }}</p>
                        @enderror

                        @error('correct_answer')
                            <p class="error-message">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Submit Button -->
                    <div class="form-actions">
                        <button type="submit" class="btn-submit">
                            Add Question to Quiz
                        </button>

                        <a href="{{ route('courses.show', $course->id) }}" id="done-btn" class="btn-done">
                            Done Adding Questions
                        </a>
                    </div>
                </form>

                <!-- Previously Added Questions (Optional) -->
                @if ($course->quizzes && $course->quizzes->count() > 0)
                    <div class="previous-questions">
                        <h3>Previously Added Questions</h3>

                        <div class="questions-table-container">
                            <table class="questions-table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Question</th>
                                        <th>Options</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($course->quizzes as $index => $quiz)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $quiz->question }}</td>
                                            <td>
                                                <ul class="options-list">
                                                    @foreach (json_decode($quiz->options) as $option)
                                                        <li class="{{ $option === $quiz->correct_answer ? 'correct-answer' : '' }}">
                                                            {{ $option }}
                                                            {{ $option === $quiz->correct_answer ? '✓' : '' }}
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        /* General styles */
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f5f7fa;
            color: #333;
            margin: 0;
            padding: 0;
        }

        /* Container */
        .container {
            max-width: 900px;
            margin: 0 auto;
            padding: 20px;
        }

        /* Course Details */
        .course-details {
            text-align: center;
            padding: 30px 20px;
        }

        .course-details h1 {
            font-size: 28px;
            margin-bottom: 10px;
        }

        .course-description {
            font-size: 18px;
            color: #666;
            margin-bottom: 20px;
        }

        .course-difficulty {
            font-size: 16px;
            color: #666;
            margin-bottom: 20px;
        }

        .difficulty-badge {
            padding: 8px 15px;
            border-radius: 20px;
            font-size: 18px;
            display: inline-block;
        }

        .beginner {
            background-color: #4CAF50;
            color: white;
        }

        .intermediate {
            background-color: #FFC107;
            color: black;
        }

        .advanced {
            background-color: #F44336;
            color: white;
        }

        /* Button Styles */
        .btn {
            display: inline-block;
            padding: 12px 20px;
            font-size: 16px;
            font-weight: bold;
            border-radius: 8px;
            text-decoration: none;
            text-align: center;
            transition: all 0.3s ease-in-out;
            cursor: pointer;
        }

        .btn-my-course {
            background-color: #007bff;
            color: white;
            margin-bottom: 20px;
        }

        .btn-my-course:hover {
            background-color: #0056b3;
        }

        /* Quiz Creation Section */
        .quiz-creation-section {
            max-width: 800px;
            margin: 0 auto;
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
        }

        .quiz-creation-section h2 {
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 20px;
            text-align: center;
        }

        .quiz-creation-section h3 {
            font-size: 20px;
            font-weight: 600;
            margin-bottom: 15px;
            margin-top: 30px;
        }

        /* Alert Info */
        .alert-info {
            background-color: #e6f7ff;
            border-left: 4px solid #1890ff;
            color: #0c5460;
            padding: 15px;
            margin-bottom: 20px;
        }

        .alert-title {
            font-weight: bold;
            margin-bottom: 5px;
        }

        /* Form Styles */
        .quiz-form {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            font-weight: bold;
            margin-bottom: 8px;
            color: #333;
            font-size: 14px;
        }

        .help-text {
            font-size: 14px;
            color: #666;
            margin-bottom: 10px;
        }

        input[type="text"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
            box-sizing: border-box;
        }

        input[type="text"]:focus {
            outline: none;
            border-color: #007bff;
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
        }

        .error-message {
            color: #dc3545;
            font-size: 12px;
            margin-top: 5px;
        }

        /* Option Items */
        .option-item {
            display: flex;
            align-items: flex-start;
            margin-bottom: 15px;
            transition: all 0.3s ease;
        }

        .option-item:hover {
            background-color: #f8f9fa;
        }

        .option-input-container {
            flex: 1;
        }

        input[type="radio"] {
            width: 18px;
            height: 18px;
            margin-right: 10px;
            margin-top: 10px;
            cursor: pointer;
        }

        .remove-option {
            color: #e53e3e;
            cursor: pointer;
            font-size: 18px;
            transition: all 0.2s ease;
            margin-left: 10px;
        }

        .remove-option:hover {
            color: #c53030;
            transform: scale(1.2);
        }

        /* Option Controls */
        .option-controls {
            display: flex;
            justify-content: space-between;
            margin-top: 10px;
            margin-bottom: 15px;
        }

        .add-option-btn {
            font-size: 14px;
            color: #007bff;
            background: none;
            border: none;
            cursor: pointer;
            padding: 0;
            text-decoration: underline;
            transition: color 0.3s ease;
        }

        .add-option-btn:hover {
            color: #0056b3;
        }

        .option-counter {
            font-size: 14px;
            color: #666;
        }

        /* Form Actions */
        .form-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .btn-submit {
            background-color: #007bff;
            color: white;
            font-weight: bold;
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }

        .btn-submit:hover {
            background-color: #0056b3;
        }

        .btn-done {
            color: #ffffff;
            background-color: #616d85;
            border-radius: 5px;
            padding: 10px 15px;
            text-decoration: none;
            transition: background-color 0.3s ease;
        }

        .btn-done:hover {
            background-color: #465167;
        }

        /* Previous Questions */
        .previous-questions {
            margin-top: 30px;
        }

        .questions-table-container {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 15px;
        }

        .questions-table {
            width: 100%;
            border-collapse: collapse;
        }

        .questions-table th,
        .questions-table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        .questions-table th {
            background-color: #f5f5f5;
            font-weight: bold;
        }

        .options-list {
            list-style-type: disc;
            margin-left: 20px;
            padding-left: 0;
        }

        .correct-answer {
            font-weight: bold;
            color: #28a745;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .container {
                padding: 10px;
            }

            .course-details h1 {
                font-size: 24px;
            }

            .quiz-creation-section {
                padding: 15px;
            }

            .form-actions {
                flex-direction: column;
                gap: 10px;
            }

            .btn-submit,
            .btn-done {
                width: 100%;
                text-align: center;
            }
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const optionsContainer = document.getElementById('options-container');
            const addOptionBtn = document.getElementById('add-option-btn');
            const optionCounter = document.getElementById('option-counter');

            // Initialize with 2 options
            let optionCount = 2;

            // Function to update the option counter display
            function updateOptionCounter() {
                if (optionCounter) {
                    optionCounter.textContent = `${optionCount}/4 options`;
                }

                // Disable add button if we've reached 4 options
                if (addOptionBtn) {
                    addOptionBtn.style.display = optionCount >= 4 ? 'none' : 'inline-block';
                }
            }

            // Add a new option
            if (addOptionBtn && optionsContainer) {
                addOptionBtn.addEventListener('click', function() {
                    if (optionCount < 4) {
                        optionCount++;

                        const newOption = document.createElement('div');
                        newOption.className = 'option-item';
                        newOption.innerHTML = `
                            <input type="radio" name="correct_answer" id="option${optionCount - 1}" value="" required>
                            <div class="option-input-container">
                                <input type="text" name="options[]" required
                                    placeholder="Option ${optionCount}"
                                    oninput="document.getElementById('option${optionCount - 1}').value = this.value">
                                <span class="remove-option">&times;</span>
                            </div>
                        `;

                        optionsContainer.appendChild(newOption);

                        // Add event listener to the remove option button
                        const removeBtn = newOption.querySelector('.remove-option');
                        if (removeBtn) {
                            removeBtn.addEventListener('click', function() {
                                newOption.remove();
                                optionCount--;
                                updateOptionCounter();

                                // Renumber the options
                                const optionItems = optionsContainer.querySelectorAll('.option-item');
                                optionItems.forEach((item, index) => {
                                    const radioInput = item.querySelector('input[type="radio"]');
                                    const textInput = item.querySelector('input[type="text"]');
                                    if (radioInput) {
                                        radioInput.id = `option${index}`;
                                    }
                                    if (textInput) {
                                        textInput.placeholder = `Option ${index + 1}`;
                                        textInput.setAttribute('oninput',
                                            `document.getElementById('option${index}').value = this.value`
                                        );
                                    }
                                });
                            });
                        }

                        updateOptionCounter();
                    }
                });
            }

            // Initialize option counter
            updateOptionCounter();

            // Initial setup for the radio buttons
            const optionInputs = document.querySelectorAll('input[name="options[]"]');
            optionInputs.forEach((input, index) => {
                input.addEventListener('input', function() {
                    document.getElementById(`option${index}`).value = this.value;
                });
            });

            // Form submission handler
            const form = document.querySelector('form');
            if (form) {
                form.addEventListener('submit', function(event) {
                    // Ensure a correct answer is selected
                    const correctAnswer = document.querySelector('input[name="correct_answer"]:checked');
                    if (!correctAnswer || !correctAnswer.value) {
                        event.preventDefault();
                        alert('Please select a correct answer by clicking the radio button next to it.');
                    }
                });
            }
        });
    </script>
@endpush