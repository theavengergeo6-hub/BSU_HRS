/**
 * BSU Hostel Management - Main Script
 */

document.addEventListener('DOMContentLoaded', function() {
    // Mobile menu toggle (navbar-custom)
    var toggle = document.getElementById('navToggle');
    var mobileMenu = document.getElementById('mobileMenu');
    if (toggle && mobileMenu) {
        toggle.addEventListener('click', function() {
            mobileMenu.classList.toggle('show');
            var icon = toggle.querySelector('i');
            if (icon) {
                icon.classList.toggle('bi-list');
                icon.classList.toggle('bi-x-lg');
            }
        });
    }

    // Initialize tooltips
    if (typeof bootstrap !== 'undefined') {
        var tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
        tooltipTriggerList.forEach(function(el) { new bootstrap.Tooltip(el); });
    }
});
