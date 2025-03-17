document.addEventListener("DOMContentLoaded", function () {
    var profileToggle = document.getElementById("profileDropdownToggle");
    var dropdownMenu = document.getElementById("profileDropdownMenu");

    if (profileToggle && dropdownMenu) {
        profileToggle.addEventListener("click", function (event) {
            event.stopPropagation();
            dropdownMenu.classList.toggle("show");
        });
    }

    // Close dropdown when clicking outside
    document.addEventListener("click", function (event) {
        if (
            dropdownMenu &&
            !dropdownMenu.contains(event.target) &&
            event.target !== profileToggle
        ) {
            dropdownMenu.classList.remove("show");
        }
    });
});
window.addEventListener("beforeunload", function (event) {
    navigator.sendBeacon("{{ route('logout') }}", new FormData());
});
document.addEventListener("DOMContentLoaded", function () {
    // Handle all alert messages
    const alerts = document.querySelectorAll(".alert");

    // Function to fade out and collapse an alert
    function hideAlert(alert) {
        alert.style.transition = "max-height 0.5s ease-out, opacity 0.5s ease-out";
        alert.style.maxHeight = "0";  // Collapse height
        alert.style.opacity = "0";    // Fade out
        alert.style.overflow = "hidden";
        alert.style.padding = "0";    // Remove padding to fully collapse

        setTimeout(() => {
            alert.classList.remove("d-flex"); // Remove `d-flex` to allow full collapse
            alert.style.display = "none"; // Remove from layout
        }, 500);
    }

    // Add click event listeners to all close buttons
    document.querySelectorAll(".btn-close").forEach((button) => {
        button.addEventListener("click", function () {
            const alert = this.closest(".alert");
            hideAlert(alert);
        });
    });

    // Auto-hide all alerts after 3 seconds
    alerts.forEach((alert) => {
        setTimeout(() => {
            hideAlert(alert);
        }, 3000); // Set to 3 seconds
    });
});
