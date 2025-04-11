@extends('layouts.app')

@section('title', 'Edit Course - MentorLink')

@section('content')
    <div class="box">
        <div class="create-course-page">
            <div class="back-link">
                <a class="back-btn1" href="{{ route('courses.index') }}">Back to All Course</a>
            </div>
            <div class="container">
                <h2>Edit Course</h2>

                <!-- Display validation errors -->
              

                <form method="POST" action="{{ route('courses.update', $course->id) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <input type="text" name="title" placeholder="Course Title"
                        value="{{ old('title', $course->title) }}" required>
                    @error('title')
                        <div class="error-message">{{ $message }}</div>
                    @enderror

                    <textarea name="description" placeholder="Course Description" rows="4" required>{{ old('description', $course->description) }}</textarea>
                    @error('description')
                        <div class="error-message">{{ $message }}</div>
                    @enderror

                    <select name="category" required>
                        <option value="" disabled>Select Category</option>
                        <option value="Web Development"
                            {{ old('category', $course->category) == 'Web Development' ? 'selected' : '' }}>Web Development
                        </option>
                        <option value="Data Science"
                            {{ old('category', $course->category) == 'Data Science' ? 'selected' : '' }}>Data Science
                        </option>
                        <option value="Design" {{ old('category', $course->category) == 'Design' ? 'selected' : '' }}>Design
                        </option>
                        <option value="Business" {{ old('category', $course->category) == 'Business' ? 'selected' : '' }}>
                            Business</option>
                    </select>
                    @error('category')
                        <div class="error-message">{{ $message }}</div>
                    @enderror

                    <select name="difficulty" required>
                        <option value="" disabled>Select Difficulty</option>
                        <option value="Beginner"
                            {{ old('difficulty', $course->difficulty) == 'Beginner' ? 'selected' : '' }}>
                            Beginner</option>
                        <option value="Intermediate"
                            {{ old('difficulty', $course->difficulty) == 'Intermediate' ? 'selected' : '' }}>Intermediate
                        </option>
                        <option value="Advanced"
                            {{ old('difficulty', $course->difficulty) == 'Advanced' ? 'selected' : '' }}>
                            Advanced</option>
                    </select>
                    @error('difficulty')
                        <div class="error-message">{{ $message }}</div>
                    @enderror

                    <!-- Upload multiple videos -->
                    <label>Upload Course Videos:</label>
                    <input type="file" name="videos[]" id="videoUpload" multiple accept="video/*">
                    <ul id="videoList">
                        <span id="existingVideoList">
                            @foreach ($course->materials->where('type', 'video') as $video)
                                <li>
                                    {{ $video->name }}
                                    <button type="button" class="delete-btn" data-id="{{ $video->id }}">x</button>
                                </li>
                            @endforeach
                        </span>
                    </ul>
                    @error('videos')
                        <div class="error-message">{{ $message }}</div>
                    @enderror

                    <!-- Upload New PDFs -->
                    <label>Upload Course PDFs:</label>
                    <input type="file" name="pdfs[]" id="pdfUpload" multiple accept="application/pdf">
                    <ul id="pdfList">
                        <span id="existingPdfList">
                            @foreach ($course->materials->where('type', 'pdf') as $pdf)
                                <li>{{ $pdf->name }}
                                    <button type="button" class="delete-btn" data-id="{{ $pdf->id }}">x</button>
                                </li>
                            @endforeach
                        </span>
                    </ul>
                    @error('pdfs')
                        <div class="error-message">{{ $message }}</div>
                    @enderror

                    <button type="submit">Update Course</button>
                </form>


            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        /* Same styles as create.blade.php */

        .create-course-page {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-bottom: 20px;

        }

        .create-course-page .container {
            background-color: #fff;
            padding: 30px;
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

        .a .back-btn1 {
            padding: 10px;
            border: none;
            border-radius: 5px;
            background-color: #007bff;
            color: #fff;
            font-size: 16px;
            cursor: pointer;
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

        .existing-files {
            list-style-type: none;
            padding: 0;
        }

        .existing-files li {
            margin: 5px 0;
        }

        .existing-files a {
            color: #007bff;
            text-decoration: none;
            font-weight: bold;
        }

        .existing-files a:hover {
            text-decoration: underline;
        }


        .create-course-page .container form button:hover {
            background-color: #0056b3;
        }



        .create-course-page .back-link a {
            text-decoration: none;
        }

        .create-course-page .error-message {
            color: red;
            font-size: 14px;
            margin-bottom: 10px;
        }


        .back-btn:hover {
            background-color: white;
            color: #007bff;
        }

        .create-course-page .back-btn1 {
            padding: 10px;
            border: none;
            border-radius: 5px;
            background-color: #007bff;
            color: #fff;
            font-size: 16px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            text-align: center;
            transition: background-color 0.3s ease;
        }

        .create-course-page .back-btn1:hover {
            background-color: #0056b3;
        }

        .delete-btn {
        background-color: #dc3545!important;
        color: white;
        border: none;
        height: 15px;
        width: 15px;
        padding: 0px!important;
        border-radius: 50%;
        cursor: pointer;
        display: inline-flex;
        justify-content: center;
        align-items: center;
        margin-left: 10px;
    }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            function displayFileNames(input, listElementId, existingListElementId) {
                const fileList = document.getElementById(listElementId);
                const existingList = document.getElementById(existingListElementId);

                // Keep existing files in the list
                fileList.innerHTML = existingList ? existingList.innerHTML : "";

                if (input.files.length > 0) {
                    for (let i = 0; i < input.files.length; i++) {
                        let li = document.createElement("li");
                        li.textContent = input.files[i].name;
                        fileList.appendChild(li);
                    }
                }
            }

            document.getElementById('videoUpload').addEventListener('change', function() {
                displayFileNames(this, 'videoList', 'existingVideoList');
            });

            document.getElementById('pdfUpload').addEventListener('change', function() {
                displayFileNames(this, 'pdfList', 'existingPdfList');
            });

            // Add event listeners for delete buttons
            document.querySelectorAll('.delete-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const materialId = this.dataset.id;
                    const confirmed = confirm('Are you sure you want to delete this item?');

                    if (confirmed) {
                        // Make an AJAX request to delete the material
                        fetch(`/materials/${materialId}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                'Content-Type': 'application/json'
                            }
                        }).then(response => {
                            if (response.ok) {
                                // Remove the list item
                                this.parentElement.remove();
                            } else {
                                alert('Failed to delete the material.');
                            }
                        });
                    }
                });
            });
        });
    </script>
@endpush