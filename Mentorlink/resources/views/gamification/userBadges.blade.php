@extends('layouts.app')

@section('content')
    <div class="container py-5">

        <div class="text-center mb-4">
            <h1 class="display-4 text-primary">Your Achievements</h1>
            <p class="lead text-muted">Here are the badges you've earned for your efforts and progress!</p>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead class="thead-light">
                    <tr>
                        <th scope="col">Badge</th>
                        <th scope="col">Name</th>
                        <th scope="col">Description</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($badges as $badge)
                        <tr class="badge-row">
                            <td>
                                <!-- Badge Icon -->
                                <img src="{{ asset('badges/' . $badge['icon']) }}" alt="{{ $badge['name'] }}"
                                    class="img-fluid" style="width: 50px; height: 50px;">
                            </td>
                            <td class="data">{{ $badge['name'] }}</td>
                            <td class="data">{{ $badge['description'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

    </div>
@endsection

@push('styles')
    <style>
        /* Table Styling */
        .table {
            width: 100%;
            margin-bottom: 1rem;
            background-color: transparent;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .table th,
        .table td {

            padding: 12px;
            vertical-align: middle;
            text-align: center;
            border: 1px solid #dee2e6;
            /* Border for cells */
            border: 1px solid black;
            /* Outer border for the table */
        }

        .table-striped tbody tr:nth-of-type(odd) {
            background-color: #f8f9fa;
        }

        .data {
            font-size: 20px;
            color: #0269d0;
            /* Border for cells */
        }

        .thead-light th {
            background-color: #f8f9fa;
            color: white;
            background-color: #007bff;
            text-transform: uppercase;
            font-weight: bold;
            border-top: 2px solid #007bff;
            border-right: 2px solid #007bff;
            border-left: 2px solid #007bff;
            border-bottom: 2px solid #007bff;
            /* Darker border for the header */
        }

        .thead-light th:hover {
            background-color: #066fdf;

        }

        .table-bordered {
            border: 1px solid #dee2e6;
            /* Outer border for the table */
            border-radius: 0.25rem;
        }

        /* Hover effect and animation for rows */
        .badge-row {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .badge-row:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 15px rgba(0, 123, 255, 0.2);
            background-color: #e9f7ff;
            /* Light blue background on hover */
        }

        /* Icon styling */
        .badge-row img {
            border-radius: 50%;
            transition: transform 0.2s ease;
        }

        .badge-row img:hover {
            transform: scale(1.2);
            /* Enlarge icon on hover */
        }
    </style>
@endpush
