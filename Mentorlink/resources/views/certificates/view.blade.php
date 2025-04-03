@extends('layouts.app')

@push('styles')
    <link
        href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&family=Playfair+Display:wght@700&display=swap"
        rel="stylesheet">
    <style>
        .cert-bodyz {
            font-family: 'Montserrat', Arial, sans-serif;
            background: #f8f9fa;
            color: #333;
        }

        .cert-wrapperz {
            padding: 40px 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 80vh;
        }

        .certificate-containerz {
            background: #fff;
            padding: 50px 40px;
            border-radius: 4px;
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
            width: 800px;
            max-width: 90%;
            margin: 50px auto;
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            align-items: center;
            border: none;
            height: auto;
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

        .cert-logoz {
            width: 120px;
            height: 120px;
            margin-bottom: 20px;
            border-radius: 50%;
        }

        .cert-titlez {
            font-family: 'Playfair Display', serif;
            color: #007bff;
            font-size: 36px;
            margin: 30px 0 20px;
            position: relative;
            text-align: center;
        }

        .cert-titlez:after {
            content: '';
            display: block;
            width: 80px;
            height: 3px;
            background: #007bff;
            margin: 15px auto 0;
        }

        .cert-subtitlez {
            font-size: 16px;
            line-height: 1.6;
            margin: 8px 0;
            color: #555;
            text-align: center;
        }

        .cert-highlightz {
            font-family: 'Playfair Display', serif;
            font-size: 28px;
            color: #222;
            margin: 15px 0;
            text-align: center;
        }

        .cert-highlightz:nth-of-type(2) {
            font-size: 22px;
            color: #007bff;
        }

        .cert-mentorz {
            font-size: 20px;
            font-weight: 600;
            margin: 15px 0;
            color: #333;
            text-align: center;
        }

        .cert-datez {
            font-style: italic;
            margin-top: 25px;
            text-align: center;
            font-weight: normal;
            color: #555;
        }

        .cert-datez strong {
            color: #333;
        }

        .cert-badgez {
            position: absolute;
            top: 40px;
            right: 40px;
            width: 120px;
            height: 60px;
            border-radius: 15px;
            background: #007bff;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-weight: bold;
            font-size: 16px;
            box-shadow: 0 4px 8px rgba(0, 123, 255, 0.3);
        }

        .cert-signaturez {
            width: 200px;
            height: 1px;
            background: #007bff;
            margin: 30px auto 10px;
        }

        .cert-btn-downloadz {
            margin-top: 40px;
            padding: 12px 24px;
            font-size: 16px;
            font-weight: 600;
            color: white;
            background: #007bff;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            transition: 0.3s ease;
            display: inline-block;
            text-align: center;
        }

        .cert-btn-downloadz:hover {
            background: #0056b3;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 123, 255, 0.3);
        }

        @media print {
            .cert-btn-downloadz {
                display: none;
            }

            .certificate-containerz {
                box-shadow: none;
                margin: 0;
                padding: 0;
            }
        }
    </style>
@endpush

@section('content')
    <div class="cert-wrapperz">
        <div class="certificate-containerz">
            <div class="cert-borderz"></div>
            <div class="cert-badgez">Official</div>

            <!-- MentorLink Logo -->
            <img src="{{ asset('images/mentorlink_logo.png') }}" alt="MentorLink Logo" class="cert-logoz">

            <h1 class="cert-titlez">Certificate of Completion</h1>
            <p class="cert-subtitlez">This is to certify that</p>
            <h2 class="cert-highlightz">{{ Auth::user()->name }}</h2>
            <p class="cert-subtitlez">has successfully completed the course</p>
            <h3 class="cert-highlightz">{{ $course->title }}</h3>
            <p class="cert-subtitlez">under the mentorship of</p>

            <!-- Mentor Name -->
            <p class="cert-mentorz">{{ $mentor_name }}</p>

            <p class="cert-datez">
                <strong>Issued On:</strong>
                @if ($certificate && $certificate->issued_at)
                    {{ $certificate->issued_at->format('d M Y') }}
                @else
                    <span style="color: red;">Not Available</span>
                @endif
            </p>

            <a href="{{ route('certificate.download', $course->id) }}" class="cert-btn-downloadz">Download Certificate</a>
        </div>
    </div>
@endsection
