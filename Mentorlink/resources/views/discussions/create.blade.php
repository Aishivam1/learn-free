@extends('layouts.app')

@section('title', 'Create Discussion')

@section('content')
    <style>
        /* Page background */
        body,
        html {
            height: 100%;
            margin: 0;
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
        }

        /* Container for the entire page content */
        .page-wrapper {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        /* Full-width header */
        .navbar {
            width: 100%;
            background: white;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            padding: 15px 0;
        }

        /* Main content area */
        .content {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 40px 0;
        }

        /* Centered discussion container */
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
            /* Center the container horizontally */
        }

        /* Title */
        h2 {
            font-weight: bold;
            color: #343a40;
            text-align: center;
            transition: transform 0.3s ease-in-out;
        }

        h2:hover {
            transform: scale(1.05);
        }

        /* Form elements */
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

        .form-control:focus {
            border-color: #007bff;
            box-shadow: 0 0 10px rgba(0, 123, 255, 0.3);
        }

        /* Buttons */
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
            background: linear-gradient(135deg, #007bff, #0056b3);
            border: none;
            color: white;
        }

        .btn-secondary {
            background: linear-gradient(135deg, #6c757d, #343a40);
            border: none;
            color: white;
        }

        .btn:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        /* Footer styling - full width */
        .footer {
            width: 100%;
            text-align: center;
            padding: 20px;
            background: #f8f9fa;
            box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.1);
        }

        /* Responsive design */
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

        /* Animations */
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
                <h2>Start a Discussion in {{ $course->title }}</h2>

                <form action="{{ route('discussions.store', $course->id) }}" method="POST">
                    @csrf

                    <div class="form-group">
                        <label for="message">Discussion Message:</label>
                        <textarea name="message" id="message" rows="5" class="form-control" required
                            placeholder="Write your discussion topic here..."></textarea>
                    </div>

                    <div class="button-group">
                        <button type="submit" class="btn btn-primary">Create Discussion</button>
                        <a href="{{ route('discussion.index', $course->id) }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
