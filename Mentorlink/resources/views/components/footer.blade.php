<footer>
    <div class="footer-content" id="footer">
        <div>
            <h3>MentorLink</h3>
            <p>An innovative learning platform connecting mentors and learners through video-based courses.</p>
        </div>
        <div>
            <h3>Platform</h3>
            <p><a href="{{ route('courses.index') }}">Courses</a></p>
            <p>
                @if (isset($course) && is_object($course))
                    <a href="{{ route('discussions.index', ['courseId' => $course->id]) }}" class="btn">
                        Discussions
                    </a>
                @endif
            </p>
            <p><a href="{{ route('certificates.index') }}">Certificates</a></p>
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
@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Footer Animation (Fade-in)
            gsap.from("#footer", {
                duration: 1,
                y: 50,
                opacity: 0,
                ease: "power3.out"
            });

            // Staggered Columns Animation
            gsap.from("#footer div", {
                duration: 0.8,
                opacity: 0,
                y: 30,
                stagger: 0.2,
                ease: "power2.out"
            });

            // Social Icons Bounce Effect
            document.querySelectorAll(".social-icons a").forEach(icon => {
                icon.addEventListener("mouseenter", () => {
                    gsap.to(icon, {
                        scale: 1.2,
                        duration: 0.3,
                        ease: "power1.out"
                    });
                });
                icon.addEventListener("mouseleave", () => {
                    gsap.to(icon, {
                        scale: 1,
                        duration: 0.3,
                        ease: "power1.out"
                    });
                });
            });
        });
    </script>
@endpush
