@extends('layouts.mentor')

@section('title', 'Edit Course')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-8">Edit Course</h1>

    <!-- Added "edit-course-card" class for animation and hover effects -->
    <form action="{{ route('courses.update', $course->id) }}" method="POST" enctype="multipart/form-data" class="max-w-2xl space-y-6 transform transition-transform duration-300 hover:scale-105 edit-course-card">
        @csrf
        @method('PUT')

        <div class="mb-6">
            <label for="title" class="block text-sm font-medium text-gray-700">Course Title</label>
            <input type="text" name="title" id="title" value="{{ $course->title }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
        </div>

        <div class="mb-6">
            <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
            <textarea name="description" id="description" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">{{ $course->description }}</textarea>
        </div>

        <div class="mb-6">
            <label for="thumbnail" class="block text-sm font-medium text-gray-700">Course Thumbnail</label>
            @if($course->thumbnail)
                <img src="{{ asset($course->thumbnail) }}" alt="Current thumbnail" class="w-32 h-32 object-cover mb-2">
            @endif
            <input type="file" name="thumbnail" id="thumbnail" class="mt-1 block w-full">
        </div>

        <div class="mb-6">
            <label for="duration" class="block text-sm font-medium text-gray-700">Duration (hours)</label>
            <input type="number" name="duration" id="duration" value="{{ $course->duration }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
        </div>

        <div class="mb-6">
            <label for="difficulty" class="block text-sm font-medium text-gray-700">Difficulty Level</label>
            <select name="difficulty" id="difficulty" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                <option value="beginner" {{ $course->difficulty == 'beginner' ? 'selected' : '' }}>Beginner</option>
                <option value="intermediate" {{ $course->difficulty == 'intermediate' ? 'selected' : '' }}>Intermediate</option>
                <option value="advanced" {{ $course->difficulty == 'advanced' ? 'selected' : '' }}>Advanced</option>
            </select>
        </div>

        <div class="flex justify-end space-x-4">
            <a href="{{ route('courses.show', $course->id) }}" class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600 transition-transform duration-300">
                Cancel
            </a>
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600 transition-transform duration-300">
                Update Course
            </button>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
    // Animate the container on page load
    gsap.from(".container", {
        opacity: 0,
        y: 50,
        duration: 1,
        ease: "power2.out"
    });

    // Animate the header with a bounce effect
    gsap.from("h1", {
        opacity: 0,
        y: -30,
        duration: 1,
        ease: "bounce.out"
    });

    // Add a subtle 3D hover effect to the Update Course button
    const updateButton = document.querySelector("button[type='submit']");
    if (updateButton) {
        updateButton.addEventListener("mouseenter", () => {
            gsap.to(updateButton, { duration: 0.3, scale: 1.05, rotationY: 5 });
        });
        updateButton.addEventListener("mouseleave", () => {
            gsap.to(updateButton, { duration: 0.3, scale: 1, rotationY: 0 });
        });
    }

    // Optional: Add a hover scaling effect to the entire form card
    const editCourseCard = document.querySelector(".edit-course-card");
    if (editCourseCard) {
        editCourseCard.addEventListener("mouseenter", () => {
            gsap.to(editCourseCard, { duration: 0.3, scale: 1.02 });
        });
        editCourseCard.addEventListener("mouseleave", () => {
            gsap.to(editCourseCard, { duration: 0.3, scale: 1 });
        });
    }
</script>
@endsection
