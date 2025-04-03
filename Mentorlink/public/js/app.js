document.addEventListener("DOMContentLoaded", function () {
    // === Navbar Functionality ===
    const navbarToggler = document.getElementById("navbarToggler");
    const navbarContent = document.getElementById("navbarContent");

    if (navbarToggler && navbarContent) {
        navbarToggler.addEventListener("click", function () {
            navbarToggler.classList.toggle("active");
            navbarContent.classList.toggle("show");
        });
    }

    // === Discussions Dropdown Functionality ===
    const discussionsToggle = document.getElementById("discussionsDropdownToggle");
    const discussionsMenu = document.getElementById("discussionsDropdownMenu");

    if (discussionsToggle && discussionsMenu) {
        // Initially hide the dropdown
        discussionsMenu.style.display = "none";

        discussionsToggle.addEventListener("click", function (event) {
            event.preventDefault();
            event.stopPropagation();

            // Toggle display
            const isHidden = discussionsMenu.style.display === "none";
            discussionsMenu.style.display = isHidden ? "block" : "none";

            // For desktop: Position dropdown below toggle
            if (window.innerWidth > 991) {
                const toggleRect = discussionsToggle.getBoundingClientRect();
                discussionsMenu.style.top = toggleRect.bottom + "px";
                discussionsMenu.style.left = toggleRect.left + "px";
            }
        });
    }

    // === Profile Dropdown Functionality ===
    const profileToggle = document.getElementById("profileDropdownToggle");
    const profileMenu = document.getElementById("profileDropdownMenu");

    if (profileToggle && profileMenu) {
        profileToggle.addEventListener("click", function (event) {
            event.stopPropagation();
            profileMenu.classList.toggle("show");
        });
    }

    // === Close Dropdowns When Clicking Outside ===
    document.addEventListener("click", function (event) {
        // Close discussion dropdown
        if (
            discussionsMenu &&
            discussionsToggle &&
            !discussionsToggle.contains(event.target) &&
            !discussionsMenu.contains(event.target)
        ) {
            discussionsMenu.style.display = "none";
        }

        // Close profile dropdown
        if (
            profileMenu &&
            profileToggle &&
            !profileToggle.contains(event.target) &&
            !profileMenu.contains(event.target)
        ) {
            profileMenu.classList.remove("show");
        }
    });

    // === GSAP Animations ===
    gsap.registerPlugin(ScrollTrigger);

    // Page Load Animation
    gsap.from("header, .hero, .section", {
        opacity: 0,
        y: 50,
        duration: 1,
        stagger: 0.2,
    });

    // Scroll Animations
    gsap.utils.toArray(".fade-in").forEach((section) => {
        gsap.from(section, {
            opacity: 0,
            y: 50,
            scrollTrigger: {
                trigger: section,
                start: "top 80%",
                toggleActions: "play none none reverse",
            },
        });
    });

    // Button Hover Effects
    document.querySelectorAll("button").forEach((button) => {
        button.addEventListener("mouseenter", () =>
            gsap.to(button, { scale: 1.1, duration: 0.2 })
        );
        button.addEventListener("mouseleave", () =>
            gsap.to(button, { scale: 1, duration: 0.2 })
        );
    });

    // Text Effect
    gsap.from(".animated-text", {
        opacity: 0,
        y: -20,
        duration: 1,
        stagger: 0.1,
    });

    // Alert handling
    const alerts = document.querySelectorAll(".alert");

    function hideAlert(alert) {
        alert.style.transition = "max-height 0.5s ease-out, opacity 0.5s ease-out";
        alert.style.maxHeight = "0";
        alert.style.opacity = "0";
        alert.style.overflow = "hidden";
        alert.style.padding = "0";

        setTimeout(() => {
            alert.classList.remove("d-flex");
            alert.style.display = "none";
        }, 500);
    }

    document.querySelectorAll(".btn-close").forEach((button) => {
        button.addEventListener("click", function () {
            const alert = this.closest(".alert");
            hideAlert(alert);
        });
    });

    // Auto-hide alerts after 3 seconds
    alerts.forEach((alert) => {
        setTimeout(() => {
            hideAlert(alert);
        }, 3000);
    });
});

// Logout before unload
window.addEventListener("beforeunload", function (event) {
    navigator.sendBeacon("{{ route('logout') }}", new FormData());
});