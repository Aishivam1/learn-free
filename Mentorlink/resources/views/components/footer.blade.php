<footer>
    <div class="footer-content" id="footer">
        <div>
            <h3>MentorLink</h3>
            <p>An innovative learning platform connecting mentors and learners through video-based courses.</p>
        </div>
        <div>
            <h3>Platform</h3>
            <p><a href="{{ route('courses.index') }}">Courses</a></p>
            <p><a href="{{ route('leaderboard') }}">Leaderboard</a></p>
               <p><a href="{{ route('discussion.index', ['courseId' => isset($course) && is_object($course) ? $course->id : 3]) }}">Discussions</a></p>
            <p><a href="{{ route('certificates.index') }}">My Certificates</a></p>
        </div>
        <div>
            <h3>Company</h3>
            <p><a href="{{ route('about') }}">About Us</a></p>
            <p><a href="{{ route('careers') }}">Careers</a></p>
            <p><a href="{{ route('press') }}">Press</a></p>
            <p><a href="{{ route('contact') }}">Contact</a></p>
        </div>
        <div>
            <h3>Resources</h3>
            <p><a href="{{ route('help-center') }}">Help Center</a></p>
            <p><a href="{{ route('terms-of-service') }}">Terms of Service</a></p>
            <p><a href="{{ route('privacy-policy') }}">Privacy Policy</a></p>
            <p><a href="{{ route('faq') }}">FAQ</a></p>
        </div>
    </div>
    <div class="social-icons">
        <a href="#"><i class="fab fa-twitter"></i></a>
        <a href="#"><i class="fab fa-facebook-f"></i></a>
    </div>
</footer>
