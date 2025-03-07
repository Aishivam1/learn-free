<div class="course-card">
    <img src="{{ $course->thumbnail }}" alt="{{ $course->title }}">
    <h3>{{ $course->title }}</h3>
    <p>Mentor: {{ $course->mentor->name }}</p>
    <p>Rating: {{ $course->rating }}</p>
    <p>Duration: {{ $course->duration }}</p>
    <a href="{{ route('courses.show', $course->id) }}" class="btn btn-primary">Enroll</a>
</div>
