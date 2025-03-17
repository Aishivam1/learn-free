@extends('layouts.app')

@section('title', 'MentorLink')

@section('content')
  <!-- Hero Section -->
  <section class="hero">
    <div class="tagline">🌟 Welcome to the future of online learning 🌟</div>
    <h1>Elevate Your Learning</h1>
    <h2>with Expert Mentors</h2>
    <p>
      Engage with video-based courses, earn certificates, and climb the leaderboard as you master new skills.
      Join a community of passionate learners and expert mentors.
    </p>
    <div class="buttons">
      <a class="explore" href="{{ route('courses.index') }}">Explore Courses</a>
      <a class="create" href="{{ route('register') }}">Create Account</a>
    </div>
  </section>

  <!-- Stats Section -->
  <section class="stats">
    <div class="stat">
      <h3>100+</h3>
      <p>Free Courses</p>
    </div>
    <div class="stat">
      <h3>10k+</h3>
      <p>Active Learners</p>
    </div>
    <div class="stat">
      <h3>500+</h3>
      <p>Expert Mentors</p>
    </div>
    <div class="stat">
      <h3>95%</h3>
      <p>Completion Rate</p>
    </div>
  </section>

  <!-- Featured Courses Section -->
  <section class="featured-courses">
    <h2>Featured Courses</h2>
    <p>
      Explore our most popular courses taught by expert mentors from around the world.
    </p>
    <div class="courses">
      <div class="course">

        <div class="content">
          <h3>Introduction to Web Development</h3>
          <p>
            Learn the fundamentals of HTML, CSS, and JavaScript to build modern web applications.
          </p>
          <div class="author">By Sarah Johnson</div>
        </div>
        <div class="badge">Beginner</div>
      </div>
      <div class="course">
        <div class="content">
          <h3>Advanced Data Science with Python</h3>
          <p>
            Master data analysis, visualization, and machine learning techniques using Python.
          </p>
          <div class="author">By Michael Chen</div>
        </div>
        <div class="badge">Intermediate</div>
      </div>
      <div class="course">
        <div class="content">
          <h3>UX/UI Design Principles</h3>
          <p>
            Learn how to create intuitive, user-friendly interfaces through research and design.
          </p>
          <div class="author">By Emily Rodriguez</div>
        </div>
        <div class="badge">Intermediate</div>
      </div>
      <div class="course">
        <div class="content">
          <h3>Business Leadership and Management</h3>
          <p>
            Develop essential leadership skills to effectively manage teams and drive success.
          </p>
          <div class="author">By Robert Williams</div>
        </div>
        <div class="badge">Advanced</div>
      </div>
    </div>
    <div class="view-all">
      <a href="{{ route('courses.index') }}">View All Courses</a>
    </div>
  </section>

  <!-- Features Section -->
  <section class="features">
    <h2>Platform Features</h2>
    <p>
      Discover the tools and features designed to make your learning journey effective, engaging, and rewarding.
    </p>
    <div class="feature-list">
      <div class="feature">
        <h3>Video-Based Learning</h3>
        <p>Engage with immersive video content created by industry experts and educators.</p>
      </div>
      <div class="feature">
        <h3>Earn Certificates</h3>
        <p>Complete courses and quizzes to earn certificates that showcase your achievements.</p>
      </div>
      <div class="feature">
        <h3>Leaderboard Ranking</h3>
        <p>Compete with peers and track your progress on our gamified leaderboard system.</p>
      </div>
      <div class="feature">
        <h3>Community Discussions</h3>
        <p>Participate in course-specific discussions to enhance your learning experience.</p>
      </div>
      <div class="feature">
        <h3>Interactive Quizzes</h3>
        <p>Test your knowledge with MCQ quizzes and ensure comprehensive understanding.</p>
      </div>
      <div class="feature">
        <h3>Curated Content</h3>
        <p>Access quality-controlled content that has been approved by platform administrators.</p>
      </div>
    </div>
  </section>

  <!-- Testimonials Section -->
  <section class="testimonials">
    <h2>What Our Learners Say</h2>
    <p>
      Discover how MentorLink has transformed the learning experience for our community.
    </p>
    <div class="testimonial-list">
      <div class="testimonial">
        <p>"The interactive quizzes and video lectures kept me engaged throughout the course. I'm proud to have earned my certificate!"</p>
        <div class="author">Alex Thompson<br>Web Developer</div>
      </div>
      <div class="testimonial">
        <p>"As a mentor, I've found the platform incredibly intuitive. The discussion features allow me to connect with my students in meaningful ways."</p>
        <div class="author">Jessica Liu<br>Data Science Instructor</div>
      </div>
      <div class="testimonial">
        <p>"The gamification elements made learning fun! Competing on the leaderboard motivated me to complete courses faster."</p>
        <div class="author">Marcus Johnson<br>Business Student</div>
      </div>
    </div>
  </section>

  <!-- Call to Action Section -->
  <section class="cta">
    <h2>Ready to Start Your Learning Journey?</h2>
    <p>
      Join thousands of learners already mastering new skills on MentorLink. Sign up today and get access to high-quality video courses, interactive quizzes, and a supportive community.
    </p>
    <div class="buttons">
      <a class="create" href="{{ route('register') }}">Create Free Account</a>
      <a class="browse" href="{{ route('courses.index') }}">Browse Courses</a>
    </div>
  </section>
@endsection
