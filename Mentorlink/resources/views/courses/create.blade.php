@extends('layouts.mentor')

@section('title', 'Create New Course')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-8">Create New Course</h1>

    <!-- Added hover effect on the form container -->
    <form action="{{ route('courses.store') }}" method="POST" enctype="multipart/form-data" class="max-w-2xl space-y-6 transform transition-transform duration-300 hover:scale-105">
        @csrf
        <div class="mb-6">
            <label for="title" class="block text-sm font-medium text-gray-700">Course Title</label>
            <input type="text" name="title" id="title" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
        </div>

        <div class="mb-6">
            <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
            <textarea name="description" id="description" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"></textarea>
        </div>

        <div class="mb-6">
            <label for="thumbnail" class="block text-sm font-medium text-gray-700">Course Thumbnail</label>
            <input type="file" name="thumbnail" id="thumbnail" class="mt-1 block w-full">
        </div>

        <div class="mb-6">
            <label for="duration" class="block text-sm font-medium text-gray-700">Duration (hours)</label>
            <input type="number" name="duration" id="duration" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
        </div>

        <div class="mb-6">
            <label for="difficulty" class="block text-sm font-medium text-gray-700">Difficulty Level</label>
            <select name="difficulty" id="difficulty" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                <option value="beginner">Beginner</option>
                <option value="intermediate">Intermediate</option>
                <option value="advanced">Advanced</option>
            </select>
        </div>

        <div class="flex justify-end">
            <!-- Added GSAP 3D hover effect on the button -->
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600 transform transition-transform duration-300">
                Create Course
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

    // Animate the header (Create New Course) with a bounce effect
    gsap.from("h1", {
        opacity: 0,
        y: -30,
        duration: 1,
        ease: "bounce.out"
    });

    // Add a subtle 3D hover effect to the submit button
    const submitButton = document.querySelector("button[type='submit']");
    if (submitButton) {
        submitButton.addEventListener("mouseenter", () => {
            gsap.to(submitButton, { duration: 0.3, scale: 1.05, rotationY: 5 });
        });
        submitButton.addEventListener("mouseleave", () => {
            gsap.to(submitButton, { duration: 0.3, scale: 1, rotationY: 0 });
        });
    }
</script>
@endsection
