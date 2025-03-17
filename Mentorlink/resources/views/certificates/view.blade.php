@extends('layouts.app')

@section('content')
<div class="container text-center">
    <h2>Certificate of Completion</h2>
    <p>Congratulations, {{ Auth::user()->name }}!</p>
    <p>You have successfully completed <strong>{{ $course->title }}</strong>.</p>

    <div class="card mt-4 mx-auto" style="max-width: 500px;">
        <div class="card-body">
            <h4 class="card-title">Certificate Details</h4>
            <p><strong>Course Name:</strong> {{ $course->title }}</p>
            <p><strong>Issued On:</strong> {{ $certificate->created_at->format('d M Y') }}</p>
            <p><strong>Certificate ID:</strong> {{ $certificate->id }}</p>
        </div>
    </div>

    <a href="{{ route('certificate.download', $course->id) }}" class="btn btn-success mt-3">
        Download Certificate
    </a>
</div>
@endsection
