@extends('layouts.app')

@section('title', 'Create Discussion')

@section('content')
    <style>
        .container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .content {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 40px 0;
        }


        .discussion-container {
            width: 90%;
            max-width: 600px;
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
            animation: fadeIn 0.8s ease-in-out;
            display: flex;
            flex-direction: column;
            align-items: center;
            margin: 0 auto;
        }

        h2 {
            font-weight: bold;
            color: #343a40;
            text-align: center;
            transition: transform 0.3s ease-in-out;
        }

        h2:hover {
            transform: scale(1.05);
        }

        .form-group {
            width: 100%;
            margin-bottom: 15px;
        }

        .form-group label {
            font-weight: bold;
            color: #495057;
        }

        .form-control {
            width: 100%;
            border-radius: 8px;
            border: 2px solid #dee2e6;
            transition: border 0.3s ease-in-out;
            padding: 10px;
        }

        textarea.form-control {
            min-height: 150px;
        }

        .form-control:focus {
            border-color: #007bff;
            box-shadow: 0 0 10px rgba(0, 123, 255, 0.3);
        }

        .button-group {
            width: 100%;
            display: flex;
            justify-content: space-between;
            margin-top: 15px;
        }

        .btn {
            border-radius: 8px;
            font-weight: bold;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            padding: 10px 15px;
            width: 48%;
            text-align: center;
        }

        .btn-primary {
            background: linear-gradient(135deg, #0a76d4, #007fe0);
            border: none;
            color: white;
        }

        .btn-secondary {
            background: linear-gradient(135deg, #4f87b8, #007fe0);
            border: none;
            color: white;
        }

        .btn:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .info-text {
            color: #6c757d;
            font-size: 0.9em;
            margin-top: 5px;
        }

        @media (max-width: 768px) {
            .discussion-container {
                width: 95%;
                padding: 20px;
            }

            .btn {
                width: 100%;
                margin-top: 10px;
            }

            .button-group {
                flex-direction: column;
            }
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>

    <div class="page-wrapper">
        <!-- Content (this will be centered) -->
        <div class="content">
            <div class="discussion-container">
                <h2>Start a Discussion{{ isset($course) ? ' in ' . $course->title : '' }}</h2>

                @if (session('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                @endif

                <form action="{{ route('discussions.store') }}" method="POST">
                    @csrf

                    <!-- Course Selection -->
                    <div class="form-group">
                        <label for="course_id">Select Course:</label>
                        <select name="course_id" id="course_id" class="form-control" required>
                            <option value="">-- Choose a Course --</option>
                            @if (isset($courses))
                                @foreach ($courses as $courseOption)
                                    <option value="{{ $courseOption->id }}"
                                        {{ request('course_id') == $courseOption->id ? 'selected' : '' }}>
                                        {{ $courseOption->title }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                        <p class="info-text">Only your enrolled courses are shown</p>
                    </div>

                    <!-- Discussion Message -->
                    <div class="form-group">
                        <label for="message">Discussion Message:</label>
                        <textarea name="message" id="message" rows="5" class="form-control" required
                            placeholder="Write your discussion topic here...">{{ old('message') }}</textarea>
                    </div>

                    <div class="button-group">
                        <button type="submit" class="btn btn-primary">Create Discussion</button>
                        <a href="{{ route('discussions.index') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>

            </div>
        </div>
    </div>
@endsection
