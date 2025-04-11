@extends('layouts.app')

@section('title', 'Edit Quiz Question')

@section('content')
    <div class="container">
        <!-- Back Button -->
        <a href="{{ route('courses.show', $course->id) }}" class="btn btn-back">
            Back to Course
        </a>
        <div class="course-info">
            <div>
                <p><strong>Course:</strong> {{ $course->title }}</p>
            </div>
            <div>
                <p><strong>Difficulty:</strong>
                    <span
                        class="difficulty-badge {{ $course->difficulty === 'beginner' ? 'beginner' : ($course->difficulty === 'intermediate' ? 'intermediate' : 'advanced') }}">
                        {{ ucfirst($course->difficulty) }}
                    </span>
                </p>
            </div>
        </div>

        <div class="quiz-edit-wrapper">
            <h1>Edit Quiz Question</h1>

         
            <!-- Question Navigation -->
            <div class="question-navigation">
                @php
                    // Find current question index
                    $currentIndex = 0;
                    $prevQuiz = null;
                    $nextQuiz = null;

                    foreach ($relatedQuizzes as $index => $relQuiz) {
                        if ($relQuiz->id == $quiz->id) {
                            $currentIndex = $index;
                            break;
                        }
                    }

                    // Get previous and next quiz
                    if ($currentIndex > 0) {
                        $prevQuiz = $relatedQuizzes[$currentIndex - 1];
                    }

                    if ($currentIndex < count($relatedQuizzes) - 1) {
                        $nextQuiz = $relatedQuizzes[$currentIndex + 1];
                    }
                @endphp

                <div class="navigation-info">
                    <span>Question {{ $currentIndex + 1 }} of {{ count($relatedQuizzes) }}</span>
                </div>

                <div class="navigation-controls">
                    @if ($prevQuiz)
                        <a href="{{ route('quiz.edit.specific', ['course_id' => $course->id, 'quiz_id' => $prevQuiz->id]) }}"
                            class="btn-nav prev-btn">
                            <i class="fas fa-chevron-left"></i> Previous Question
                        </a>
                    @else
                        <button class="btn-nav prev-btn disabled" disabled>
                            <i class="fas fa-chevron-left"></i> Previous Question
                        </button>
                    @endif

                    @if ($nextQuiz)
                        <a href="{{ route('quiz.edit.specific', ['course_id' => $course->id, 'quiz_id' => $nextQuiz->id]) }}"
                            class="btn-nav next-btn">
                            Next Question <i class="fas fa-chevron-right"></i>
                        </a>
                    @else
                        <button class="btn-nav next-btn disabled" disabled>
                            Next Question <i class="fas fa-chevron-right"></i>
                        </button>
                    @endif
                </div>
            </div>

            <!-- Edit Form -->
            <div class="quiz-edit-form-container">
                <form action="{{ route('quiz.update', $quiz->id) }}" method="POST" class="quiz-form">
                    @csrf
                    @method('POST')

                    <!-- Hidden Course ID -->
                    <input type="hidden" name="course_id" value="{{ $course->id }}">

                    <!-- Question -->
                    <div class="form-group">
                        <label for="question">Question:</label>
                        <input type="text" name="question" id="question" required placeholder="Enter your question"
                            value="{{ $quiz->question }}">
                        @error('question')
                            <p class="error-message">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Options Section -->
                    <div class="form-group">
                        <label>Options:</label>
                        <p class="help-text">Select the correct answer by clicking the radio button.</p>

                        <div id="options-container">
                            @php
                                $options = is_array($quiz->options)
                                    ? $quiz->options
                                    : json_decode($quiz->options, true);
                            @endphp

                            @foreach ($options as $index => $option)
                                <div class="option-item">
                                    <input type="radio" name="correct_answer" id="option{{ $index }}"
                                        value="{{ $option }}"
                                        {{ $option === $quiz->correct_answer ? 'checked' : '' }} required>
                                    <div class="option-input-container">
                                        <input type="text" name="options[]" required
                                            placeholder="Option {{ $index + 1 }}" value="{{ $option }}"
                                            oninput="document.getElementById('option{{ $index }}').value = this.value">
                                        @if ($index > 1)
                                            <span class="remove-option">&times;</span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="option-controls">
                            <button type="button" id="add-option-btn" class="add-option-btn">
                                + Add Another Option
                            </button>
                            <span class="option-counter" id="option-counter">{{ count($options) }}/4 options</span>
                        </div>

                        @error('options')
                            <p class="error-message">{{ $message }}</p>
                        @enderror

                        @error('correct_answer')
                            <p class="error-message">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Form Actions -->
                    <div class="form-actions">
                        <button type="submit" class="btn-update">
                            Update Question
                        </button>

                        <a href="{{ route('courses.show', $course->id) }}" class="btn-cancel">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>

            <!-- Delete Section -->
            <div class="delete-section">
                <form action="{{ route('quiz.destroy', $quiz->id) }}" method="POST" class="delete-form-full">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-delete-full"
                        onclick="return confirm('Are you sure you want to delete this question?')">
                        Delete This Question
                    </button>
                </form>
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
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }

        /* Back Button */
        .btn-back {
            display: inline-block;
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: bold;
            margin-bottom: 20px;
            transition: background-color 0.3s;
        }

        .btn-back:hover {
            background-color: #0056b3;
        }

        /* Quiz Edit Wrapper */
        .quiz-edit-wrapper {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 25px;
        }

        .quiz-edit-wrapper h1 {
            font-size: 24px;
            margin-bottom: 20px;
            color: #333;
            text-align: center;
        }

        /* Course Info */
        .course-info {
            margin-bottom: 20px;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 8px;
            border-left: 4px solid #007bff;
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
        }

        .course-info p {
            margin: 5px 0;
        }

        /* Question Navigation */
        .question-navigation {
            margin: 20px 0;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 8px;
        }

        .navigation-info {
            text-align: center;
            font-weight: bold;
            margin-bottom: 15px;
            color: #555;
        }

        .navigation-controls {
            display: flex;
            justify-content: space-between;
            gap: 10px;
        }

        .btn-nav {
            padding: 10px 15px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            font-size: 14px;
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            transition: background-color 0.3s;
        }

        .btn-nav:hover {
            background-color: #0056b3;
        }

        .btn-nav.disabled {
            background-color: #b3d7ff;
            cursor: not-allowed;
            opacity: 0.7;
        }

        .btn-nav i {
            margin: 0 8px;
        }

        /* Difficulty Badge */
        .difficulty-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 14px;
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

        /* Quiz Edit Form */
        .quiz-edit-form-container {
            margin-bottom: 30px;
        }

        .quiz-form {
            background: #fff;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            font-weight: bold;
            margin-bottom: 8px;
            color: #333;
            font-size: 16px;
        }

        .help-text {
            font-size: 14px;
            color: #666;
            margin-bottom: 10px;
        }

        input[type="text"] {
            width: 100%;
            padding: 12px;
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
            display: flex;
            align-items: center;
        }

        input[type="radio"] {
            width: 18px;
            height: 18px;
            margin-right: 10px;
            margin-top: 12px;
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
            margin-top: 30px;
        }

        .btn-update {
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

        .btn-update:hover {
            background-color: #0056b3;
        }

        .btn-cancel {
            background-color: #6c757d;
            color: white;
            font-weight: bold;
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            text-decoration: none;
            transition: background-color 0.3s ease;
        }

        .btn-cancel:hover {
            background-color: #5a6268;
        }

        /* Delete Section */
        .delete-section {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
        }

        .delete-section h3 {
            color: #dc3545;
            margin-bottom: 10px;
            font-size: 18px;
        }

        .warning-text {
            color: #dc3545;
            margin-bottom: 15px;
            font-size: 14px;
        }

        .btn-delete-full {
            background-color: #dc3545;
            color: white;
            font-weight: bold;
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
            transition: background-color 0.3s ease;
        }

        .btn-delete-full:hover {
            background-color: #c82333;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .container {
                padding: 10px;
            }

            .quiz-edit-wrapper {
                padding: 15px;
            }

            .form-actions {
                flex-direction: column;
                gap: 10px;
            }

            .btn-update,
            .btn-cancel {
                width: 100%;
                text-align: center;
                margin-bottom: 10px;
            }

            .navigation-controls {
                flex-direction: column;
                gap: 10px;
            }

            .btn-nav {
                width: 100%;
                justify-content: center;
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

            // Initialize with existing options count
            let optionCount = optionsContainer.querySelectorAll('.option-item').length;

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
                                const optionItems = optionsContainer.querySelectorAll(
                                    '.option-item');
                                optionItems.forEach((item, index) => {
                                    const radioInput = item.querySelector(
                                        'input[type="radio"]');
                                    const textInput = item.querySelector(
                                        'input[type="text"]');
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

            // Setup existing remove option buttons
            const existingRemoveBtns = document.querySelectorAll('.remove-option');
            existingRemoveBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    const optionItem = this.closest('.option-item');
                    optionItem.remove();
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
            });

            // Initialize option counter
            updateOptionCounter();

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

                    // Ensure we have at least 2 options
                    const options = document.querySelectorAll('input[name="options[]"]');
                    if (options.length < 2) {
                        event.preventDefault();
                        alert('You must have at least 2 options for each question.');
                    }
                });
            }

            // Add keyboard shortcuts for navigation
            document.addEventListener('keydown', function(e) {
                // Left arrow key for previous question
                if (e.key === 'ArrowLeft') {
                    const prevButton = document.querySelector('.prev-btn:not(.disabled)');
                    if (prevButton && prevButton.tagName === 'A') {
                        prevButton.click();
                    }
                }

                // Right arrow key for next question
                if (e.key === 'ArrowRight') {
                    const nextButton = document.querySelector('.next-btn:not(.disabled)');
                    if (nextButton && nextButton.tagName === 'A') {
                        nextButton.click();
                    }
                }
            });
        });
    </script>
@endpush
