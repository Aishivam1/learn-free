@extends('layouts.app')

@section('content')
    <div class="content-wrapper">
        <div class="container">
            <h2>Contact Us</h2>
            <p>If you have any questions, feel free to reach out to us.</p>

            <div class="contact-form">
                <form action="#" method="POST">
                    @csrf
                    <input type="text" name="name" placeholder="Your Name" required>
                    <input type="email" name="email" placeholder="Your Email" required>
                    <textarea name="message" placeholder="Your Message" rows="4" required></textarea>
                    <button type="submit">Send Message</button>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f7fa;
            color: #333;
            min-height: 100vh;
        }

        .content-wrapper {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: calc(100vh - 140px); /* Adjust 140px based on your header + footer height */
            padding: 20px;
        }

        .container {
            background-color: #fff;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
            max-width: 600px;
            width: 100%;
        }

        .contact-form {
            margin-top: 20px;
        }

        .contact-form input,
        .contact-form textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .contact-form button {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
        }

        .contact-form button:hover {
            background-color: #0056b3;
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            document.querySelector('.contact-form form').addEventListener('submit', function(e) {
                e.preventDefault();
                alert('Thank you for reaching out! We will respond soon.');
                this.reset();
            });
        });
    </script>
@endpush