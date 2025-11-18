document.addEventListener('DOMContentLoaded', function() {
    // --- Logout Button Functionality ---
    const logoutBtn = document.getElementById('logout-btn');
    if (logoutBtn) {
        logoutBtn.addEventListener('click', (event) => {
            // Prevent the default link behavior (GET request) immediately.
            event.preventDefault();
            // Confirm with the user before logging out
            if (confirm('Are you sure you want to logout?')) {
                // Create a hidden form to submit the POST request for logout
                const logoutForm = document.createElement('form');
                logoutForm.method = 'POST';
                logoutForm.action = '/logout'; // Laravel's default logout route

                const csrfTokenMeta = document.querySelector('meta[name="csrf-token"]');
                if (!csrfTokenMeta) {
                    console.error('CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token');
                    alert('Could not log out. CSRF token is missing.');
                    return;
                }
                logoutForm.innerHTML = `<input type="hidden" name="_token" value="${csrfTokenMeta.getAttribute('content')}">`;
                document.body.appendChild(logoutForm);
                logoutForm.submit();
            }
        });
    }
});