@extends('layouts.app')

@section('content')
    <div class="content-wrapper">
        <div class="container">
            <h2>Contact Us</h2>
            <p>If you have any questions <i class="fa-solid fa-circle-question fa-shake fa-sm" style="color: #007bfd;"></i>,
                feel free to reach out to us.</p>

            <div class="contact-details">
                <p><strong>📍 Address:</strong> 1234 Mentor Street, Knowledge City, ML 56789</p>
                <p><strong>📧 Email:</strong> support@mentorlink.com</p>
                <p><strong>📞 Phone:</strong> +1 234 567 890</p>
                <p><strong>⏰ Office Hours:</strong> Monday - Friday, 9 AM - 6 PM</p>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        body {
            color: rgb(0, 123, 255);
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
            min-height: calc(100vh - 140px);
            /* Adjust 140px based on your header + footer height */
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

        .contact-details {
            margin-top: 20px;
            text-align: left;
        }

        .contact-details p {
            font-size: 18px;
            margin: 10px 0;
        }

        .contact-details strong {
            color: #007bff;
        }
    </style>
@endpush

@push('scripts')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            gsap.to(".contact-details", {
                duration: 2.5,
                ease: "power1.out",
                y: +20
            });
        });

        document.addEventListener("DOMContentLoaded", function() {
            document.querySelector('.contact-form form').addEventListener('submit', function(e) {
                e.preventDefault();
                alert('Thank you for reaching out! We will respond soon.');
                this.reset();
            });
        });
    </script>
@endpush
