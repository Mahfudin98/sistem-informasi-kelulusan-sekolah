/**
 * app.js - General interactions
 */

document.addEventListener('DOMContentLoaded', () => {

    // 1. Alert Dismissal
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        // Automatically hide success alerts after 5 seconds
        if(alert.classList.contains('alert-success')) {
             setTimeout(() => {
                 alert.style.opacity = '0';
                 setTimeout(() => alert.remove(), 300);
             }, 5000);
        }
        
        // Add click-to-dismiss functionality
        alert.addEventListener('click', () => {
             alert.style.opacity = '0';
             setTimeout(() => alert.remove(), 300);
        });
        alert.style.cursor = 'pointer';
        alert.title = 'Klik untuk menutup';
    });

    // 2. Password Toggle
    const pwToggle = document.getElementById('pwToggle');
    if (pwToggle) {
        pwToggle.addEventListener('click', function() {
            const input = document.getElementById('password');
            if (input.type === 'password') {
                input.type = 'text';
                this.textContent = '🚫'; // Icon for hide
            } else {
                input.type = 'password';
                this.textContent = '👁'; // Icon for show
            }
        });
    }

    // 3. Lookup Form Loading State
    const lookupForm = document.getElementById('lookupForm');
    if (lookupForm) {
        lookupForm.addEventListener('submit', function() {
            const btn = document.getElementById('submitBtn');
            const text = btn.querySelector('.btn-text');
            const spinner = btn.querySelector('.btn-spinner');
            
            // Only show spinner if form is valid (native validation)
            if (this.checkValidity()) {
                 btn.disabled = true;
                 btn.style.opacity = '0.8';
                 text.textContent = 'Mengecek...';
                 spinner.classList.remove('hidden');
                 
                 // Note: we don't preventDefault here, let the form submit natively
                 // The loading state will just show until the next page renders
            }
        });
        
        // Auto-scroll to result card if it exists
        const resultCard = document.getElementById('resultCard');
        if (resultCard) {
             resultCard.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    }
});
