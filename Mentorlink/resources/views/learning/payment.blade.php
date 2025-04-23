@extends('layouts.app')

@section('title', 'Course Payment - MentorLink')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="back-btn-container">
            <a href="{{ route('courses.my') }}" class="btn btn-my-course">Back to My Courses</a>
        </div>

        <div class="payment-container">
            <h2 class="text-3xl font-bold text-center mb-6">Course Payment</h2>

            <!-- Add Time and User Info Display -->
            <div class="time-info-container">
                <div class="time-info-box">
                    <div class="info-row">
                        <span class="info-label">Current Date and Time (UTC):</span>
                        <span id="utc-time" class="info-value">{{ $currentTime }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Current User's Login:</span>
                        <span id="username" class="info-value">{{ Auth::user()->username }}</span>
                    </div>
                </div>
            </div>
            <div class="payment-info">
                <h3 class="course-title">{{ $course->title }}</h3>
                <p class="amount">Amount: ₹{{ number_format($paymentData['amount'], 2) }}</p>
                <p class="reference">Reference: {{ $paymentReference }}</p>
            </div>

            <div class="qr-code-container">
                <h3 class="text-xl font-semibold mb-4">Scan with Google Pay</h3>
                <div class="qr-wrapper">
                    {!! $qrCode !!}
                </div>
                <p class="scan-instruction">Scan this QR code with Google Pay to make payment</p>
            </div>

            <div class="mobile-payment">
                <button onclick="openGooglePay()" class="gpay-button">
                    Open in Google Pay
                </button>
            </div>

            <div class="payment-steps">
                <h4 class="text-lg font-semibold mb-3">Steps to Complete Payment:</h4>
                <ol>
                    <li>1. Scan the QR code or click "Open in Google Pay" button</li>
                    <li>2. Complete the payment of ₹{{ number_format($paymentData['amount'], 2) }}</li>
                    <li>3. Take a screenshot of your payment confirmation</li>
                    <li>4. Submit the verification details below</li>
                </ol>
            </div>

            <div class="verification-form">
                <h3 class="text-xl font-semibold mb-4">Verify Your Payment</h3>
                <form action="{{ route('payment.callback') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="ref" value="{{ $paymentReference }}">
                    <input type="hidden" name="current_time" id="current_time" value="{{ $currentTime }}">
                    <input type="hidden" name="username" value="{{ Auth::user()->username }}">

                    <div class="form-group">
                        <label for="transaction_id">Google Pay Transaction ID*</label>
                        <input type="text" id="transaction_id" name="transaction_id"
                            class="form-control @error('transaction_id') is-invalid @enderror" pattern="[A-Za-z0-9]+"
                            minlength="8" required>
                        <small class="help-text">Enter the transaction ID from your Google Pay payment</small>
                        @error('transaction_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="payment_screenshot">Payment Screenshot*</label>
                        <input type="file" id="payment_screenshot" name="payment_screenshot"
                            class="form-control @error('payment_screenshot') is-invalid @enderror" accept="image/*"
                            required>
                        <small class="help-text">Upload screenshot of your payment confirmation</small>
                        @error('payment_screenshot')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="upi_id">Your UPI ID*</label>
                        <!-- Change this in your form -->
                        <input type="text" id="upi_id" name="upi_id"
                            class="form-control @error('upi_id') is-invalid @enderror"
                            pattern="[a-zA-Z0-9._-]+@[a-zA-Z0-9-]+" placeholder="Example: pankajdave199-5@okicici" required>
                        <small class="help-text">
                            Valid UPI ID formats:<br>
                            • username-number@bank (Example: pankajdave199-5@okicici)<br>
                            • PhoneNumber@upi (Example: 9876543210@upi)<br>
                            • Username@bank (Example: pankaj@okicici)<br>
                            Common banks: okaxis, okhdfcbank, okicici, oksbi, ybl, paytm
                        </small>
                        @error('upi_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <button type="submit" class="verify-button">
                        Verify Payment
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .payment-container {
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .payment-info {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .course-title {
            font-size: 24px;
            font-weight: bold;
            color: #333;
            margin-bottom: 15px;
        }

        .amount {
            font-size: 20px;
            font-weight: bold;
            color: #2c3e50;
        }

        .qr-code-container {
            text-align: center;
            padding: 30px;
            border: 2px dashed #e0e0e0;
            border-radius: 8px;
            margin: 25px 0;
        }

        .qr-wrapper {
            display: inline-block;
            padding: 15px;
            background: white;
            border-radius: 8px;
            margin: 15px 0;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .gpay-button {
            background: #007bff;
            color: white;
            padding: 14px 28px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: bold;
            width: 100%;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .gpay-button:hover {
            background: white;
            color: #0056b3;
            border: 2px solid #007bff;
        }

        .payment-steps {
            margin: 30px 0;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 8px;
        }

        .payment-steps ol {
            padding-left: 20px;
        }

        .payment-steps li {
            margin: 10px 0;
            color: #666;
        }

        .verification-form {
            margin-top: 30px;
            padding: 25px;
            background: #f8f9fa;
            border-radius: 8px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
        }

        .form-control {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 16px;
            transition: border-color 0.3s ease;
        }

        .form-control:focus {
            border-color: #007bff;
            outline: none;
        }

        .help-text {
            display: block;
            margin-top: 5px;
            font-size: 14px;
            color: #666;
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

        .verify-button {
            background: #007bff;
            color: white;
            padding: 14px 28px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: bold;
            width: 100%;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .verify-button:hover {
            background: #0056b3;
        }

        .invalid-feedback {
            color: #dc3545;
            font-size: 14px;
            margin-top: 5px;
        }

        /* Dark Mode */
        @media (prefers-color-scheme: dark) {
            .payment-container {
                background-color: #1e1e1e;
                color: #ddd;
            }

            .payment-info,
            .payment-steps,
            .verification-form {
                background: #2d2d2d;
            }

            .course-title {
                color: #fff;
            }

            .form-control {
                background: #333;
                border-color: #444;
                color: #fff;
            }

            .form-group label {
                color: #ddd;
            }

            .help-text {
                color: #999;
            }

            .payment-steps li {
                color: #bbb;
            }
        }

        /* Responsive Design */
        @media (max-width: 640px) {
            .payment-container {
                margin: 10px;
                padding: 15px;
            }

            .qr-code-container {
                padding: 15px;
            }

            .course-title {
                font-size: 20px;
            }

            .amount {
                font-size: 18px;
            }
        }
    </style>
@endpush
@push('scripts')
    <script>
        // UTC Time Update Function
        function updateUTCTime() {
            const now = new Date();
            const utcString = now.toISOString().slice(0, 19).replace('T', ' ');
            const timeDisplay = document.getElementById('utc-time');
            const timeInput = document.getElementById('current_time');

            if (timeDisplay) timeDisplay.textContent = utcString;
            if (timeInput) timeInput.value = utcString;
        }

        // Update time every second
        setInterval(updateUTCTime, 1000);
        updateUTCTime(); // Initial update

        function openGooglePay() {
            const upiString = @json($upiString);

            if (/Android|iPhone|iPad|iPod/i.test(navigator.userAgent)) {
                window.location.href = upiString;
            } else {
                alert('Please scan the QR code with Google Pay app on your mobile device');
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('form');
            const upiInput = document.getElementById('upi_id');
            const transactionInput = document.getElementById('transaction_id');
            const screenshotInput = document.getElementById('payment_screenshot');
            const username = document.getElementById('username')?.textContent || '';

            // Valid bank handles
            const validBankHandles = ['okaxis', 'okhdfcbank', 'okicici', 'oksbi', 'ybl', 'paytm', 'upi'];

            // UPI ID validation pattern
            const upiPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9-]+$/;

            function validateUpiId(upiId) {
                if (!upiPattern.test(upiId)) {
                    return 'Invalid UPI ID format';
                }

                const bankHandle = upiId.split('@')[1]?.toLowerCase();
                if (!validBankHandles.includes(bankHandle)) {
                    return `Invalid bank handle. Valid handles: ${validBankHandles.join(', ')}`;
                }

                return '';
            }

            // Real-time UPI ID validation
            upiInput.addEventListener('input', function(e) {
                const upiId = e.target.value.trim();
                const error = validateUpiId(upiId);

                if (error) {
                    this.setCustomValidity(error);
                    e.target.classList.add('is-invalid');

                    if (!document.querySelector('.upi-feedback')) {
                        const feedback = document.createElement('div');
                        feedback.className = 'invalid-feedback upi-feedback';
                        feedback.textContent = error;
                        e.target.parentNode.appendChild(feedback);
                    } else {
                        document.querySelector('.upi-feedback').textContent = error;
                    }
                } else {
                    this.setCustomValidity('');
                    e.target.classList.remove('is-invalid');

                    const feedback = document.querySelector('.upi-feedback');
                    if (feedback) {
                        feedback.remove();
                    }
                }
            });

            // Form submission validation
            form.addEventListener('submit', function(e) {
                e.preventDefault();

                // Update current time before submission
                updateUTCTime();

                // Check screenshot
                const file = screenshotInput.files[0];
                if (!file) {
                    alert('Please upload payment screenshot');
                    screenshotInput.focus();
                    return false;
                }

                // Validate file size
                if (file.size > 2 * 1024 * 1024) {
                    alert('File size must be less than 2MB');
                    screenshotInput.focus();
                    return false;
                }

                // Check transaction ID
                const transactionId = transactionInput.value.trim();
                if (!transactionId.match(/^[A-Za-z0-9]{8,30}$/)) {
                    alert('Please enter a valid transaction ID (8-30 alphanumeric characters)');
                    transactionInput.focus();
                    return false;
                }

                // Check UPI ID
                const upiId = upiInput.value.trim();
                const upiError = validateUpiId(upiId);
                if (upiError) {
                    alert(upiError);
                    upiInput.focus();
                    return false;
                }

                // Create and show processing overlay
                const overlay = document.createElement('div');
                overlay.className = 'processing-overlay';
                overlay.innerHTML = `
                    <div class="processing-content">
                        <div class="spinner"></div>
                        <p>Verifying payment...</p>
                        <small>Time: ${document.getElementById('current_time').value}</small>
                        <small>User: ${username}</small>
                    </div>
                `;
                document.body.appendChild(overlay);

                // Add loading state
                const submitButton = form.querySelector('button[type="submit"]');
                submitButton.disabled = true;
                submitButton.innerHTML = 'Verifying payment...';

                // Submit form
                form.submit();
            });

            // File type validation
            screenshotInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (!file) return;

                // Check file type
                const validTypes = ['image/jpeg', 'image/png', 'image/jpg'];
                if (!validTypes.includes(file.type)) {
                    alert('Please upload a valid image file (JPG, JPEG, or PNG)');
                    this.value = '';
                    return;
                }

                // Check file size
                if (file.size > 2 * 1024 * 1024) {
                    alert('File size must be less than 2MB');
                    this.value = '';
                    return;
                }

                // Create image object to check dimensions
                const img = new Image();
                img.src = URL.createObjectURL(file);
                img.onload = function() {
                    URL.revokeObjectURL(this.src);
                    if (this.width < 300 || this.height < 300) {
                        alert('Image dimensions must be at least 300x300 pixels');
                        screenshotInput.value = '';
                    }
                };
            });
        });
    </script>

    <style>
        .processing-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.7);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }

        .processing-content {
            background: white;
            padding: 30px;
            border-radius: 10px;
            text-align: center;
        }

        .spinner {
            width: 40px;
            height: 40px;
            border: 4px solid #f3f3f3;
            border-top: 4px solid #007bff;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 0 auto 20px;
        }

        .processing-content p {
            margin: 10px 0;
            font-size: 16px;
        }

        .processing-content small {
            display: block;
            color: #666;
            margin: 5px 0;
        }

        /* Add these new styles */
        .time-info-container {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .time-info-box {
            border: 1px solid #dee2e6;
            padding: 12px;
            border-radius: 6px;
            background: white;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 0;
            border-bottom: 1px solid #eee;
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .info-label {
            color: #6c757d;
            font-size: 14px;
            font-weight: 500;
        }

        .info-value {
            font-family: 'Consolas', monospace;
            font-size: 14px;
            color: #007bff;
            font-weight: 600;
            padding: 4px 8px;
            background: #f8f9fa;
            border-radius: 4px;
        }

        /* Dark mode support */
        @media (prefers-color-scheme: dark) {
            .time-info-container {
                background: #2d2d2d;
            }

            .time-info-box {
                background: #1e1e1e;
                border-color: #444;
            }

            .info-row {
                border-bottom-color: #444;
            }

            .info-label {
                color: #adb5bd;
            }

            .info-value {
                background: #2d2d2d;
                color: #5fa9ff;
            }
        }

        /* Responsive design */
        @media (max-width: 640px) {
            .info-row {
                flex-direction: column;
                align-items: flex-start;
            }

            .info-value {
                margin-top: 4px;
                width: 100%;
                text-align: center;
            }
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        @media (prefers-color-scheme: dark) {
            .processing-content {
                background: #2d2d2d;
                color: #fff;
            }

            .processing-content small {
                color: #bbb;
            }
        }
    </style>
@endpush
