/**
 * admin.js - Admin dashboard specific interactions
 */

document.addEventListener('DOMContentLoaded', () => {

    // 1. Sidebar Toggle Logic is now handled inline in layouts/admin.php 
    // to ensure bulletproof execution on all devices.

    // 2. Delete Confirmation
    const deleteForms = document.querySelectorAll('.form-delete');
    deleteForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const name = this.dataset.name || 'data ini';
            
            if (confirm(`Apakah Anda yakin ingin menghapus ${name}? Tindakan ini tidak dapat dibatalkan.`)) {
                this.submit();
            }
        });
    });

});
