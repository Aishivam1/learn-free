@extends('layouts.app')

@section('title', 'Reported Discussions')

@section('content')
<div class="container py-4">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h3 mb-0">Reported Discussions</h1>
            <p class="text-muted">Review and moderate reported content</p>
        </div>
        <div class="col-md-4 text-md-end">
            <a href="{{ route('discussions.index') }}" class="btn btn-outline-primary">
                <i class="fas fa-arrow-left"></i> Back to Discussions
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($reportedDiscussions->isEmpty())
        <div class="alert alert-info">
            No reported discussions found.
        </div>
    @else
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th>User</th>
                        <th>Course</th>
                        <th>Content</th>
                        <th>Reports</th>
                        <th>Posted</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($reportedDiscussions as $discussion)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <img src="{{ $discussion->user->avatar ? asset('avatar/' . $discussion->user->avatar) : asset('images/default-avatar.png') }}" 
                                    alt="Avatar" class="rounded-circle me-2" style="width: 30px; height: 30px;">
                                                                   {{ $discussion->user->name }}
                                </div>
                            </td>
                            <td>{{ $discussion->course->title }}</td>
                            <td>{{ Str::limit($discussion->message, 100) }}</td>
                            <td>
                                <span class="badge bg-danger">
                                    {{ count($discussion->reports['reported_by'] ?? []) }} {{ Str::plural('report', count($discussion->reports['reported_by'] ?? [])) }}
                                </span>
                            </td>
                            <td>{{ $discussion->created_at->format('M d, Y h:i A') }}</td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('discussions.show', $discussion->id) }}" class="btn btn-sm btn-outline-primary">View</a>
                                    <button type="button" class="btn btn-sm btn-outline-primary dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-expanded="false">
                                        <span class="visually-hidden">Toggle Dropdown</span>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <form action="{{ route('discussions.dismiss-reports', $discussion->id) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="dropdown-item">Dismiss Reports</button>
                                            </form>
                                        </li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <form action="{{ route('discussions.delete', $discussion->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this discussion?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="dropdown-item text-danger">Delete Discussion</button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection