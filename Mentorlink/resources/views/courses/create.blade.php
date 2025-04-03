@extends('layouts.app')

@section('title', 'Create Course - MentorLink')

@section('content')
    <div classs="box">
        <div class="create-course-page">
            <div class="back-btn-container">
                <a href="{{ route('courses.index') }}" class="btn btn-my-course">Back to All Courses</a>
            </div>
            <div class="container">

                <h2>Create a New Course</h2>

                <!-- Display validation errors -->
                @if ($errors->any())
                    <div class="error-message">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('courses.store') }}" enctype="multipart/form-data">
                    @csrf
                    <input type="text" name="title" placeholder="Course Title" value="{{ old('title') }}" required>
                    @error('title')
                        <div class="error-message">{{ $message }}</div>
                    @enderror

                    <textarea name="description" placeholder="Course Description" rows="4" required>{{ old('description') }}</textarea>
                    @error('description')
                        <div class="error-message">{{ $message }}</div>
                    @enderror

                    <select name="category" required>
                        <option value="" disabled selected>Select Category</option>
                        <option value="Web Development" {{ old('category') == 'Web Development' ? 'selected' : '' }}>Web
                            Development</option>
                        <option value="Data Science" {{ old('category') == 'Data Science' ? 'selected' : '' }}>Data Science
                        </option>
                        <option value="Design" {{ old('category') == 'Design' ? 'selected' : '' }}>Design</option>
                        <option value="Business" {{ old('category') == 'Business' ? 'selected' : '' }}>Business</option>
                    </select>
                    @error('category')
                        <div class="error-message">{{ $message }}</div>
                    @enderror

                    <select name="difficulty" required>
                        <option value="" disabled selected>Select Difficulty</option>
                        <option value="Beginner" {{ old('difficulty') == 'Beginner' ? 'selected' : '' }}>Beginner</option>
                        <option value="Intermediate" {{ old('difficulty') == 'Intermediate' ? 'selected' : '' }}>
                            Intermediate
                        </option>
                        <option value="Advanced" {{ old('difficulty') == 'Advanced' ? 'selected' : '' }}>Advanced</option>
                    </select>
                    @error('difficulty')
                        <div class="error-message">{{ $message }}</div>
                    @enderror

                    <!-- Upload multiple videos -->
                    <label>Upload Course Videos:</label>
                    <input type="file" name="videos[]" id="videoUpload" multiple accept="video/*">
                    <ul id="videoList"></ul>
                    @error('videos')
                        <div class="error-message">{{ $message }}</div>
                    @enderror

                    <!-- Upload multiple PDFs -->
                    <label>Upload Course PDFs:</label>
                    <input type="file" name="pdfs[]" id="pdfUpload" multiple accept="application/pdf">
                    <ul id="pdfList"></ul>
                    @error('pdfs')
                        <div class="error-message">{{ $message }}</div>
                    @enderror

                    <button type="submit">Submit for Approval</button>
                </form>


            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        /* Apply styles only within the Create Course page */

        .create-course-page {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-bottom: 20px;
        }

        .create-course-page .container {
            background-color: #fff;
            padding: 30px;
            /* Reduce padding inside the form */
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            width: 400px;
            text-align: center;
        }


        .create-course-page .container h2 {
            margin-bottom: 20px;
            color: #007bff;
        }

        .create-course-page .container form {
            display: flex;
            flex-direction: column;
        }

        .create-course-page .container form input,
        .create-course-page .container form textarea,
        .create-course-page .container form select {
            margin-bottom: 20px;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }

        .create-course-page .container form button {
            padding: 10px;
            border: none;
            border-radius: 5px;
            background-color: #007bff;
            color: #fff;
            font-size: 16px;
            cursor: pointer;
        }

        .create-course-page .container form button:hover {
            background-color: #0056b3;
        }

        .create-course-page .back-link {
            margin-top: 20px;
            font-size: 14px;
        }

        .create-course-page .back-link a {
            text-decoration: none;
        }

        .create-course-page .error-message {
            color: red;
            font-size: 14px;
            margin-bottom: 10px;
        }

        .box {
            position: relative;
            height: 100vh;
            width: 100%;
        }

        .back-link,
        .back-link a {
            background-color: #007bff;
            color: white !important;
            margin-top: 15px;
            margin-right: 15px;
            padding: 10px;
            border-radius: 5px;
            text-decoration: none;
            height: 40px;
        }

        .back-link:hover {
            background-color: white;
            color: #007bff;
        }

        .btn {
            display: inline-block;
            padding: 12px 12px;
            font-size: 16px;
            font-weight: bold;
            border-radius: 8px;
            text-decoration: none;
            text-align: center;
            height: 40px;
            transition: all 0.3s ease-in-out;
        }


        .back-btn-container .btn-my-course {
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            padding: 10px;
            text-decoration: none;
            display: inline-block;
        }

        .back-btn-container .btn-my-course:hover {
            background-color: #0056b3;
        }
    </style>
@endpush
@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            function displayFileNames(input, listElementId) {
                const fileList = document.getElementById(listElementId);
                fileList.innerHTML = ""; // Clear previous list
                if (input.files.length > 0) {
                    for (let i = 0; i < input.files.length; i++) {
                        let li = document.createElement("li");
                        li.textContent = input.files[i].name;
                        fileList.appendChild(li);
                    }
                }
            }

            document.getElementById('videoUpload').addEventListener('change', function() {
                displayFileNames(this, 'videoList');
            });

            document.getElementById('pdfUpload').addEventListener('change', function() {
                displayFileNames(this, 'pdfList');
            });
        });
    </script>
@endpush
