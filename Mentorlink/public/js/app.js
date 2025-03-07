
document.addEventListener('DOMContentLoaded', function() {
  var profileToggle = document.getElementById('profileDropdownToggle');
  var dropdownMenu = document.getElementById('profileDropdownMenu');

  if (profileToggle && dropdownMenu) {
    profileToggle.addEventListener('click', function(event) {
      event.stopPropagation();
      dropdownMenu.classList.toggle('show');
    });
  }

  // Close dropdown when clicking outside
  document.addEventListener('click', function(event) {
    if (dropdownMenu && !dropdownMenu.contains(event.target) && event.target !== profileToggle) {
      dropdownMenu.classList.remove('show');
    }
  });
});
