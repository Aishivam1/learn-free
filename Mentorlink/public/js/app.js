document.addEventListener("DOMContentLoaded", function () {
    const mainContainer = document.querySelector(".container.min-height-100vh");
    // === Navbar Functionality ===
    const navbarToggler = document.getElementById("navbarToggler");
    const navbarContent = document.getElementById("navbarContent");

    if (navbarToggler && navbarContent) {
        navbarToggler.addEventListener("click", function () {
            navbarToggler.classList.toggle("active");
            navbarContent.classList.toggle("show");
            if (mainContainer) {
                mainContainer.style.zIndex = navbarContent.classList.contains(
                    "show"
                )
                    ? "-1"
                    : "";
            }
        });
    }

    // === Unified Dropdown Functionality ===
    const dropdownToggles = document.querySelectorAll(".dropdown-toggle");
    const dropdownMenus = document.querySelectorAll(".dropdown-menu");
    const profileToggle = document.getElementById("profileDropdownToggle");
    const profileMenu = document.getElementById("profileDropdownMenu");

    // Function to close all dropdowns
    const closeAllDropdowns = (exceptMenu = null) => {
        dropdownMenus.forEach((menu) => {
            if (menu !== exceptMenu) {
                menu.style.display = "none";
                menu.classList.remove("show");
            }
        });
        if (profileMenu && profileMenu !== exceptMenu) {
            profileMenu.style.display = "none";
            profileMenu.classList.remove("show");
        }
    };

    // Setup main nav dropdowns
    dropdownToggles.forEach((toggle) => {
        toggle.addEventListener("click", function (e) {
            e.preventDefault();
            e.stopPropagation();

            const dropdownId = this.id.replace("Toggle", "Menu");
            const dropdown = document.getElementById(dropdownId);

            if (!dropdown) return;

            // Close other dropdowns
            closeAllDropdowns(dropdown);

            // Toggle current dropdown
            const isHidden =
                dropdown.style.display === "none" ||
                !dropdown.classList.contains("show");

            if (isHidden) {
                dropdown.style.display = "block";
                dropdown.classList.add("show");

                // Position dropdown for desktop
                if (window.innerWidth > 991) {
                    const toggleRect = toggle.getBoundingClientRect();
                    dropdown.style.top = toggleRect.bottom + "px";
                    dropdown.style.left = toggleRect.left + "px";
                }
            } else {
                dropdown.style.display = "none";
                dropdown.classList.remove("show");
            }

            // Manage container z-index
            const mainContainer = document.querySelector(
                ".container.min-height-100vh"
            );
            if (mainContainer) {
                mainContainer.style.zIndex = isHidden ? "-1" : "";
            }
        });
    });

    // Setup profile dropdown
    if (profileToggle && profileMenu) {
        // Initially hide the profile dropdown
        profileMenu.style.display = "none";

        profileToggle.addEventListener("click", function (e) {
            e.preventDefault();
            e.stopPropagation();

            // Close other dropdowns
            closeAllDropdowns(profileMenu);

            // Toggle profile dropdown
            const isHidden =
                profileMenu.style.display === "none" ||
                !profileMenu.classList.contains("show");

            if (isHidden) {
                profileMenu.style.display = "block";
                profileMenu.classList.add("show");

                // Position profile dropdown
                const toggleRect = profileToggle.getBoundingClientRect();
                profileMenu.style.top = toggleRect.bottom + "px";
                profileMenu.style.right =
                    window.innerWidth - toggleRect.right + "px";
                profileMenu.style.left = "auto"; // Reset left position
            } else {
                profileMenu.style.display = "none";
                profileMenu.classList.remove("show");
            }

            // Manage container z-index
            const mainContainer = document.querySelector(
                ".container.min-height-100vh"
            );
            if (mainContainer) {
                mainContainer.style.zIndex = isHidden ? "-1" : "";
            }
        });

        // Add hover animation for profile image
        profileToggle.addEventListener("mouseenter", () => {
            gsap.to(profileToggle, {
                scale: 1.1,
                duration: 0.3,
                ease: "power1.out",
            });
        });

        profileToggle.addEventListener("mouseleave", () => {
            gsap.to(profileToggle, {
                scale: 1,
                duration: 0.3,
                ease: "power1.out",
            });
        });
    }

    // Close dropdowns when clicking outside
    document.addEventListener("click", function (e) {
        if (
            !e.target.closest(".nav-item") &&
            !e.target.closest(".profile-dropdown") &&
            !e.target.closest(".dropdown-menu")
        ) {
            closeAllDropdowns();

            // Reset container z-index
            const mainContainer = document.querySelector(
                ".container.min-height-100vh"
            );
            if (mainContainer) {
                mainContainer.style.zIndex = "";
            }
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
});
document.addEventListener("DOMContentLoaded", function () {
    console.log("Alert script loaded");

    // Ensure alerts exist
    console.log("Alerts found:", document.querySelectorAll(".alert"));

    function hideAlert(alert) {
        if (!alert) return;

        alert.classList.remove("show"); // Bootstrap fade out
        alert.style.transition = "opacity 0.5s ease-out";
        alert.style.opacity = "0";

        setTimeout(() => {
            alert.remove(); // Remove alert from DOM after fade out
        }, 500);
    }

    // Event delegation for closing alerts
    document.addEventListener("click", function (event) {
        if (event.target.classList.contains("btn-close")) {
            const alert = event.target.closest(".alert");
            hideAlert(alert);
        }
    });

    // Auto-hide alerts after 3 seconds
    document.querySelectorAll(".alert").forEach((alert) => {
        setTimeout(() => {
            hideAlert(alert);
        }, 3000);
    });
});

// Logout before unload
window.addEventListener("beforeunload", function (event) {
    navigator.sendBeacon("{{ route('logout') }}", new FormData());
});
