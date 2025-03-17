@extends('layouts.app')

@section('content')
    <div class="container">
        <h2 class="heading">My Certificates</h2>

        @if ($certificates->isEmpty())
            <p class="no-certificates">No certificates earned yet.</p>
        @else
            <div class="card" id="certificateCard">
                <table class="certificate-table">
                    <thead>
                        <tr>
                            <th>Course</th>
                            <th>Issued At</th>
                            <th>View</th>
                            <th>Download</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($certificates as $certificate)
                            <tr>
                                <td>{{ $certificate->course->title ?? 'Unknown Course' }}</td>
                                <td>{{ \Carbon\Carbon::parse($certificate->issued_at)->format('d M Y') }}</td>
                                <td>
                                    <a href="{{ asset('storage/certificates/' . $certificate->certificate_url) }}" target="_blank"
                                        class="view-link">View</a>
                                </td>
                                <td>
                                    <a href="{{ asset('storage/certificates/' . $certificate->certificate_url) }}" download
                                        class="download-link">Download</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    {{-- GSAP for Animation --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    <script>
        gsap.from("#certificateCard", {
            opacity: 0,
            y: 50,
            duration: 1,
            ease: "power2.out"
        });
    </script>

    {{-- Custom CSS --}}
    <style>
        /* General Styling */
        .container {
            max-width: 900px;
            margin: 30px auto;
            padding: 20px;
        }

        .heading {
            text-align: center;
            font-size: 26px;
            font-weight: bold;
            margin-bottom: 20px;
            color: #333;
        }

        .no-certificates {
            text-align: center;
            color: #777;
        }

        /* Card Styling */
        .card {
            background: rgba(255, 255, 255, 0.9);
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            transition: transform 0.3s ease-in-out;
        }

        .card:hover {
            transform: scale(1.02);
        }

        /* Table Styling */
        .certificate-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .certificate-table th, .certificate-table td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: center;
        }

        .certificate-table th {
            background: #f4f4f4;
            font-weight: bold;
        }

        .certificate-table tr:hover {
            background: #f9f9f9;
        }

        /* Links */
        .view-link {
            color:#007bff;
            background-color: white;
            text-decoration: none;
            padding: 5px 10px;
            font-weight: bold;
        }

        .view-link:hover {
            text-decoration: underline;
        }

        .download-link {
            color:white ;
            padding: 5px 10px;
            background-color: #007bff;
            text-decoration: none;
            font-weight: bold;
        }

        .download-link:hover {
            text-decoration: underline;
        }
    </style>
@endsection
