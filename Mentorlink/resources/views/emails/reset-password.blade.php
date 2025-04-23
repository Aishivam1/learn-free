<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Your Password - MentorLink</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            line-height: 1.6;
            color: #333333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f9f9f9;
        }
        .container {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .logo {
            text-align: center;
            margin-bottom: 30px;
        }
        .button {
            display: inline-block;
            padding: 12px 24px;
            background-color: #007bff;
            color: #ffffff !important;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            margin: 20px 0;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eeeeee;
            font-size: 0.9em;
            color: #666666;
            text-align: center;
        }
        .warning {
            background-color: #fff8e1;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
            font-size: 0.9em;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">
            <h1 style="color: #007bff;">MentorLink</h1>
        </div>

        <h2>Hello {{ $user->name }},</h2>
        
        <p>You recently requested to reset your password for your MentorLink account. Click the button below to reset it:</p>
        
        <div style="text-align: center;">
            <a href="{{ $resetLink }}" class="button">Reset Your Password</a>
        </div>

        <div class="warning">
            <strong>Important:</strong> This password reset link will expire in 60 minutes for security reasons.
        </div>

        <p>If you did not request a password reset, please ignore this email or contact support if you have concerns.</p>
        
        <p>For security, this request was received from: {{ request()->ip() }} at {{ now()->format('Y-m-d H:i:s') }} UTC</p>

        <div style="margin: 20px 0;">
            <p><strong>Having trouble with the button above?</strong> Copy and paste this URL into your web browser:</p>
            <p style="word-break: break-all; font-size: 0.9em; color: #666666;">{{ $resetLink }}</p>
        </div>

        <div class="footer">
            <p>MentorLink - Your Learning Journey Partner</p>
            <p style="font-size: 0.8em;">This is an automated message, please do not reply to this email.</p>
        </div>
    </div>
</body>
</html>