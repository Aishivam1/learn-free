@extends('layouts.app')

@section('title', $course->title . ' - Create Quiz')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <!-- Hidden input for course ID -->
        <input type="hidden" name="course_id" value="{{ $course->id }}">

        <!-- Back to Courses Button -->
        @if ($course->enrollments->contains('user_id', auth()->id()))
            <a href="{{ route('courses.my') }}" class="btn back-btn">My Courses</a>
        @else
            <a href="{{ route(request('from') === 'pending' ? 'admin.courses.pending' : 'courses.index') }}"
                class="btn back-btn">Back to All Courses</a>
        @endif

        <div class="course-details space-y-8">
            <h1 class="text-3xl font-bold text-center">Title: {{ $course->title }}</h1>
            <h1 class="text-3xl font-bold text-center"><strong>👨‍🏫 Mentor:</strong> {{ $course->mentor->name }}</h1>
            <p class="text-lg text-gray-700 text-center">Description: {{ $course->description }}</p>
            <p class="text-md text-gray-600 text-center"><strong>📊 Difficulty:</strong>
                <span
                    class="px-3 py-1 rounded-full text-3xl
                {{ $course->difficulty === 'beginner' ? 'bg-green-500' : ($course->difficulty === 'intermediate' ? 'bg-yellow-500' : 'bg-red-500') }}">
                    {{ ucfirst($course->difficulty) }}
                </span>
            </p>

            <!-- Create Quiz Form Section -->
            <div class="quiz-creation-section">
                <h2 class="text-2xl font-semibold mb-4 text-center">📝 Create Quiz Question</h2>

                <!-- Success Message -->

                <!-- Explanation Text -->
                <div class="bg-blue-50 border-l-4 border-blue-500 text-blue-700 p-4 mb-6" role="alert">
                    <p class="font-bold">Add Questions One By One</p>
                    <p>Fill out this form to add a question to your quiz. You can add as many questions as needed by
                        submitting this form multiple times.</p>
                </div>

                <form action="{{ route('quiz.store', $course->id) }}" method="POST"
                    class="bg-white shadow-lg rounded-lg p-6">
                    @csrf

                    <!-- Question -->
                    <div class="mb-6">
                        <label for="question" class="block text-gray-700 text-sm font-bold mb-2">Question:</label>
                        <input type="text" name="question" id="question" required
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                            placeholder="Enter your question">
                        @error('question')
                            <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Options Section -->
                    <div class="mb-6">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Options:</label>
                        <p class="text-sm text-gray-600 mb-2">Select the correct answer by clicking the radio button.</p>

                        <div id="options-container">
                            <!-- Option 1 -->
                            <div class="option-item mb-3 flex items-start">
                                <input type="radio" name="correct_answer" id="option0" value="" required
                                    class="mt-2 mr-2">
                                <div class="w-full">
                                    <input type="text" name="options[]" required
                                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                        placeholder="Option 1"
                                        oninput="document.getElementById('option0').value = this.value">
                                </div>
                            </div>

                            <!-- Option 2 -->
                            <div class="option-item mb-3 flex items-start">
                                <input type="radio" name="correct_answer" id="option1" value="" required
                                    class="mt-2 mr-2">
                                <div class="w-full">
                                    <input type="text" name="options[]" required
                                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                        placeholder="Option 2"
                                        oninput="document.getElementById('option1').value = this.value">
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-between mt-2">
                            <button type="button" id="add-option-btn" class="text-sm text-blue-500 hover:underline">
                                + Add Another Option
                            </button>
                            <span class="text-sm text-gray-500" id="option-counter">2/4 options</span>
                        </div>

                        @error('options')
                            <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                        @enderror

                        @error('correct_answer')
                            <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Submit Button -->
                    <div class="flex items-center justify-between">
                        <button type="submit"
                            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg">
                            Add Question to Quiz
                        </button>

                        <a href="{{ route('courses.show', $course->id) }}" id="done-btn"
                            class="text-gray-600 hover:underline">
                            Done Adding Questions
                        </a>
                    </div>
                </form>

                <!-- Previously Added Questions (Optional) -->
                @if ($course->quizzes && $course->quizzes->count() > 0)
                    <div class="mt-8">
                        <h3 class="text-xl font-semibold mb-4">Previously Added Questions</h3>

                        <div class="bg-white shadow-lg rounded-lg p-4">
                            <table class="min-w-full">
                                <thead>
                                    <tr>
                                        <th class="px-4 py-2 text-left">#</th>
                                        <th class="px-4 py-2 text-left">Question</th>
                                        <th class="px-4 py-2 text-left">Options</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($course->quizzes as $index => $quiz)
                                        <tr class="border-t">
                                            <td class="px-4 py-2">{{ $index + 1 }}</td>
                                            <td class="px-4 py-2">{{ $quiz->question }}</td>
                                            <td class="px-4 py-2">
                                                <ul class="list-disc ml-4">
                                                    @foreach (json_decode($quiz->options) as $option)
                                                        <li
                                                            class="{{ $option === $quiz->correct_answer ? 'font-bold text-green-600' : '' }}">
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
        }

        .course-details {
            text-align: center;
            padding: 30px 20px;
        }

        .course-details h1 {
            font-size: 36px;
            margin-bottom: 10px;
        }

        .course-details p {
            font-size: 18px;
            color: #666;
            margin-bottom: 20px;
        }

        /* Back Button */
        .back-btn {
            display: inline-block;
            background-color: #007bff;
            color: white;
            font-size: 16px;
            font-weight: bold;
            padding: 10px 15px;
            border-radius: 8px;
            transition: background-color 0.3s ease-in-out, transform 0.2s;
        }

        .back-btn:hover {
            background-color: #0056b3;
            transform: translateY(-2px);
        }

        /* Quiz Creation Styles */
        .quiz-creation-section {
            max-width: 800px;
            margin: 0 auto;
        }

        .option-item {
            transition: all 0.3s ease;
        }

        .option-item:hover {
            background-color: #f8f9fa;
        }

        input[type="radio"] {
            width: 18px;
            height: 18px;
            cursor: pointer;
        }

        .remove-option {
            color: #e53e3e;
            cursor: pointer;
            font-size: 18px;
            transition: all 0.2s ease;
        }

        .remove-option:hover {
            color: #c53030;
            transform: scale(1.2);
        }

        /* General Styles */
        body {
            font-family: 'Arial', sans-serif;
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

        .course-details p {
            font-size: 16px;
            color: #666;
            margin-bottom: 15px;
        }

        /* Back Button */
        .back-btn {
            display: inline-block;
            background-color: #007bff;
            color: white;
            font-size: 16px;
            font-weight: bold;
            padding: 10px 15px;
            border-radius: 8px;
            text-decoration: none;
            transition: background-color 0.3s, transform 0.2s;
        }

        .back-btn:hover {
            background-color: #0056b3;
            transform: translateY(-2px);
        }

        /* Quiz Section */
        .quiz-creation-section {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
        }

        .quiz-creation-section h2 {
            text-align: center;
            font-size: 22px;
            margin-bottom: 15px;
        }

        #add-option-btn {
            margin-bottom: 15px;
        }

        /* Form Styles */
        form {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        label {
            font-weight: bold;
            display: block;
            margin-bottom: 5px;
        }

        input[type="text"],
        input[type="radio"] {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        input[type="radio"] {
            width: auto;
            margin-right: 5px;
        }

        .option-item {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }

        .option-item input[type="text"] {
            flex: 1;
        }

        /* Buttons */
        button {
            background-color: #007bff;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }

        button:hover {
            background-color: #0056b3;
        }

        #done-btn {
            color: #ffffff;
            background-color: #616d85;
            border-radius: 5px;
            padding: 10px;
        }
        #done-btn:hover{
            background-color:  #465167;
        }
        .text-center {
            text-align: center;
        }

        .text-green {
            color: rgb(9, 0, 128);
        }

        .text-red {
            color: red;
        }

        /* Table Styles */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table th,
        table td {
            padding: 10px;
            border: 1px solid #ccc;
            text-align: left;
        }

        table th {
            background-color: #f0f0f0;
        }

        /* Alert Messages */
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
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

            button {
                width: 100%;
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
                        newOption.className = 'option-item mb-3 flex items-start';
                        newOption.innerHTML = `
                            <input type="radio" name="correct_answer" id="option${optionCount - 1}" value="" required class="mt-2 mr-2">
                            <div class="w-full relative">
                                <input type="text" name="options[]" required
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                    placeholder="Option ${optionCount}"
                                    oninput="document.getElementById('option${optionCount - 1}').value = this.value">
                                <span class="remove-option absolute right-3 top-2">&times;</span>
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
