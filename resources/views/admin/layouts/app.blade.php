<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Panel') - My Website</title>
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Styles -->
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    @stack('styles')
</head>
<body class="admin-body">
    <div class="sidebar">
        <h2>Admin Panel</h2>
        <nav>
            <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">Dashboard</a>
            <a href="{{ route('admin.properties.list') }}" class="{{ request()->routeIs('admin.properties.*') ? 'active' : '' }}">Manage Properties</a>
            <a href="{{ route('admin.agents.index') }}" class="{{ request()->routeIs('admin.agents.*') ? 'active' : '' }}">Manage Agents</a>
            <a href="{{ route('home') }}" class="back-to-site">Back to Site</a>
        </nav>
    </div>
    <main class="main-content">
        <div class="container">
            <h1>@yield('page-title', 'Dashboard')</h1>
            @yield('content')
        </div>
    </main>
    @stack('scripts')
</body>
</html>