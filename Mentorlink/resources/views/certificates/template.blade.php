<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificate</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            padding: 50px;
            background: #f8f8f8;
        }
        .certificate-container {
            background: #fff;
            padding: 30px;
            border: 10px solid #ddd;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            width: 80%;
            margin: auto;
        }
        .logo {
            width: 150px;
            margin-bottom: 10px;
        }
        .mentor {
            font-size: 18px;
            font-weight: bold;
            margin-top: 20px;
        }
    </style>
</head>
<body>

    <div class="certificate-container">
        <!-- MentorLink Logo -->
        <img src="{{ public_path('images/mentorlink_logo.png') }}" alt="MentorLink Logo" class="logo">

        <h1>Certificate of Completion</h1>
        <p>This is to certify that</p>
        <h2>{{ $user_name }}</h2>
        <p>has successfully completed the course</p>
        <h3>{{ $course_name }}</h3>
        <p>under the mentorship of</p>

        <!-- Mentor Name -->
        <p class="mentor">{{ $mentor_name }}</p>

        <p>on {{ $completion_date }}</p>
        <p>Certificate ID: {{ $certificate_id }}</p>
    </div>

</body>
</html>
