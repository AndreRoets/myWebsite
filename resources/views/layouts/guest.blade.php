<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title') - {{ config('app.name', 'Laravel') }}</title>

    <style>
        body { font-family: sans-serif; background-color: #f4f6f9; color: #333; margin: 0; display: flex; justify-content: center; align-items: center; min-height: 100vh; }
        .auth-container { background-color: #fff; padding: 2rem; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); width: 100%; max-width: 400px; }
        .auth-title { font-size: 1.5rem; font-weight: 600; text-align: center; margin-bottom: 1.5rem; }
        .form-group { margin-bottom: 1rem; }
        .form-group label { display: block; margin-bottom: 0.5rem; font-weight: 500; }
        .form-group input { width: 100%; padding: 0.75rem; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        .form-group .error-message { color: #dc3545; font-size: 0.875em; margin-top: 0.25rem; }
        .form-group input.is-invalid { border-color: #dc3545; }
        .btn-submit { background-color: #007bff; color: white; padding: 0.75rem 1.5rem; border: none; border-radius: 4px; cursor: pointer; font-size: 1rem; width: 100%; }
        .btn-submit:hover { background-color: #0056b3; }
        .auth-link { text-align: center; margin-top: 1rem; font-size: 0.9rem; }
        .auth-link a { color: #007bff; text-decoration: none; }
        .auth-link a:hover { text-decoration: underline; }
        .alert-danger { background-color: #f8d7da; border-color: #f5c6cb; color: #721c24; padding: .75rem 1.25rem; margin-bottom: 1rem; border: 1px solid transparent; border-radius: .25rem; }
        .alert-danger ul { margin: 0; padding-left: 1.25rem; }
    </style>
</head>
<body>
    <div class="auth-container">
        <h2 class="auth-title">@yield('title')</h2>

        @if ($errors->any())
            <div class="alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </div>
</body>
</html>