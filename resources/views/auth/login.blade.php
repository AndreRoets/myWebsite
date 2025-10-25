@extends('layouts.guest')

@section('title', 'Login')

@section('content')
    <form method="POST" action="{{ route('login') }}" id="login-form">
        @csrf

        <div class="form-group">
            <label for="email">Email</label>
            {{-- Error display for email field --}}
            <span class="error-message" id="login-email-error" role="alert" style="color: #dc3545; display: none; margin-bottom: 0.5rem;"></span>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus>
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <input id="password" type="password" name="password" required>
            {{-- Error display for password field --}}
            <span class="error-message" id="login-password-error" role="alert" style="color: #dc3545; display: none; margin-bottom: 0.5rem;"></span>
        </div>

        {{-- General error message for failed credentials or other issues not tied to a specific field --}}
        <div class="form-group">
            <span class="error-message" id="login-general-error" role="alert" style="color: #dc3545; display: none; margin-bottom: 0.5rem;"></span>
        </div>

        <button type="submit" class="btn-submit">Login</button>
    </form>
    <div class="auth-link">
        @if (Route::has('register'))
        <p>Don't have an account? <a href="{{ route('register') }}">Register here</a></p>
        @endif
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const loginForm = document.getElementById('login-form');
            const emailInput = document.getElementById('email');
            const passwordInput = document.getElementById('password');
            const emailErrorSpan = document.getElementById('login-email-error');
            const passwordErrorSpan = document.getElementById('login-password-error');
            const generalErrorSpan = document.getElementById('login-general-error');

            // Function to clear all error messages and invalid states
            const clearErrors = () => {
                emailErrorSpan.textContent = '';
                emailErrorSpan.style.display = 'none';
                passwordErrorSpan.textContent = '';
                passwordErrorSpan.style.display = 'none';
                generalErrorSpan.textContent = '';
                generalErrorSpan.style.display = 'none';
                emailInput.classList.remove('is-invalid');
                passwordInput.classList.remove('is-invalid');
            };

            if (loginForm) {
                loginForm.addEventListener('submit', async function (e) {
                    e.preventDefault(); // Prevent the default form submission (page reload)

                    clearErrors(); // Clear any existing errors

                    const formData = new FormData(this);

                    try {
                        const response = await fetch(this.action, {
                            method: 'POST',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest', // Important for Laravel to detect AJAX
                                'Accept': 'application/json', // Request JSON response
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') // Add CSRF token
                            },
                            body: formData,
                            credentials: 'same-origin' // Tell fetch to include cookies
                        });

                        if (response.ok) { // HTTP status 200-299 (successful login)
                            // Laravel's default AJAX login returns 204 No Content, so 'data' will be empty.
                            // Redirect to a default path (e.g., home page or dashboard).
                            window.location.href = '/'; // You can change this to '/dashboard' or any other route
                        } else if (response.status === 422) { // Validation errors (including 'auth.failed')
                            const data = await response.json();
                            if (data.errors) {
                                // The general "These credentials do not match..." error is returned under the 'email' key.
                                // We'll display it in the general error span for better UX.
                                if (data.errors.email) {
                                    generalErrorSpan.textContent = data.errors.email[0];
                                    generalErrorSpan.style.display = 'block';
                                    emailInput.classList.add('is-invalid'); // Add class for styling
                                    passwordInput.classList.add('is-invalid'); // Also mark password as invalid
                                }
                                // Handle specific password validation errors if they exist (e.g., "password is required")
                                if (data.errors.password) {
                                    passwordErrorSpan.textContent = data.errors.password[0];
                                    passwordErrorSpan.style.display = 'block';
                                    passwordInput.classList.add('is-invalid'); // Add class for styling
                                }
                            }
                        } else { // Other server errors (e.g., 500 Internal Server Error)
                            // Handle non-JSON responses gracefully
                            const contentType = response.headers.get('content-type');
                            if (contentType && contentType.includes('application/json')) {
                                const data = await response.json();
                                generalErrorSpan.textContent = data.message || `An error occurred: ${response.statusText}`;
                            } else {
                                // This will catch 419 CSRF errors which return HTML, not JSON
                                generalErrorSpan.textContent = `An unexpected server error occurred (Status: ${response.status}). Please refresh and try again.`;
                            }
                             generalErrorSpan.style.display = 'block';

                        }
                    } catch (error) {
                        console.error('Error during login:', error);
                        generalErrorSpan.textContent = 'Network error or server unreachable. Please try again.';
                        generalErrorSpan.style.display = 'block';
                    }
                });
            }
        });
    </script>
@endsection