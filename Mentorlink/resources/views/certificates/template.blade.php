<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificate</title>
    <link
        href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&family=Playfair+Display:wght@700&display=swap"
        rel="stylesheet">
    <style>@font-face {
        font-family: 'Montserrat';
        src: url("{{ public_path('fonts/Montserrat-Regular.ttf') }}") format('truetype');
    }
    
    @font-face {
        font-family: 'Playfair Display';
        src: url("{{ public_path('fonts/PlayfairDisplay-Bold.ttf') }}") format('truetype');
    }
    
    body {
        font-family: 'Montserrat', Arial, sans-serif;
    }
    
        body {
            font-family: 'Montserrat', Arial, sans-serif;
            text-align: center;
            padding: 0;
            margin: 0;
            background: #f8f9fa;
            color: #333;
        }

        .cert-wrapperz {
            padding: 40px 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .certificate-containerz {
            background: #fff;
            padding: 50px 40px;
            border-radius: 4px;
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
            width: 800px;
            max-width: 90%;
            margin: auto;
            position: relative;
            overflow: hidden;
        }

        .cert-borderz {
            position: absolute;
            top: 15px;
            left: 15px;
            right: 15px;
            bottom: 15px;
            border: 2px solid #007bff;
            border-radius: 2px;
            pointer-events: none;
        }

        .cert-badgez {
            position: absolute;
            top: 40px;
            right: 40px;
            width: 120px;
            height: 60px;
            background: #007bff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-weight: bold;
            font-size: 16px;
            box-shadow: 0 4px 8px rgba(0, 123, 255, 0.3);
        }

        .cert-headerz {
            margin-bottom: 30px;
        }

        .cert-logoz {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            margin-bottom: 20px;
        }

        .cert-titlez {
            font-family: 'Playfair Display', serif;
            color: #007bff;
            font-size: 38px;
            margin: 30px 0 20px;
            position: relative;
        }

        .cert-titlez:after {
            content: '';
            display: block;
            width: 80px;
            height: 3px;
            background: #007bff;
            margin: 15px auto 0;
        }

        .cert-contentz {
            padding: 20px 0;
        }

        .cert-textz {
            font-size: 16px;
            line-height: 1.6;
            margin: 8px 0;
            color: #555;
        }

        .cert-namez {
            font-family: 'Playfair Display', serif;
            font-size: 30px;
            color: #222;
            margin: 15px 0;
        }

        .cert-coursez {
            font-size: 22px;
            font-weight: 600;
            color: #007bff;
            margin: 15px 0;
        }

        .cert-mentorz {
            font-size: 20px;
            font-weight: 600;
            margin: 15px 0;
            color: #333;
        }

        .cert-datez {
            font-style: italic;
            margin-top: 25px;
        }

        .cert-footerz {
            margin-top: 40px;
            border-top: 1px solid #eee;
            padding-top: 20px;
        }

        @media print {
            body {
                background: #fff;
            }

            .cert-wrapperz {
                padding: 0;
            }

            .certificate-containerz {
                box-shadow: none;
                border: none;
            }
        }
    </style>
</head>

<body>
    <div class="cert-wrapperz">
        <div class="certificate-containerz">
            <div class="cert-borderz"></div>
            <div class="cert-badgez">Official</div>

            <div class="cert-headerz">
                <!-- MentorLink Logo -->
                <img src="{{ public_path('images/mentorlink_logo.png') }}" alt="MentorLink Logo" class="cert-logoz">
            </div>

            <h1 class="cert-titlez">Certificate of Completion</h1>

            <div class="cert-contentz">
                <p class="cert-textz">This is to certify that</p>
                <h2 class="cert-namez">{{ $user_name }}</h2>
                <p class="cert-textz">has successfully completed the course</p>
                <h3 class="cert-coursez">{{ $course_name }}</h3>
                <p class="cert-textz">under the mentorship of</p>

                <!-- Mentor Name -->
                <p class="cert-mentorz">{{ $mentor_name }}</p>

                <p class="cert-datez cert-textz">on {{ $completion_date }}</p>
            </div>

            <div class="cert-footerz">
            </div>
        </div>
    </div>
</body>

</html>
