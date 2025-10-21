<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Panel') - My Website</title>
    <style>
        body { font-family: sans-serif; margin: 0; background-color: #f8f9fa; color: #333; display: flex; min-height: 100vh; }
        .sidebar { width: 250px; background-color: #343a40; color: white; padding: 1.5rem; }
        .sidebar h2 { font-size: 1.5rem; margin-bottom: 2rem; text-align: center; }
        .sidebar a { color: #adb5bd; text-decoration: none; display: block; padding: 0.75rem 1rem; border-radius: 4px; margin-bottom: 0.5rem; }
        .sidebar a:hover, .sidebar a.active { background-color: #495057; color: white; }
        .main-content { flex-grow: 1; padding: 2rem; }
        .container { max-width: 1200px; margin: auto; background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { margin-bottom: 1.5rem; }
    </style>
    @stack('styles')
</head>
<body>
    <div class="sidebar">
        <h2>Admin Panel</h2>
        <nav>
            <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">Dashboard</a>
            <a href="{{ route('admin.properties.list') }}" class="{{ request()->routeIs('admin.properties.*') ? 'active' : '' }}">Manage Properties</a>
            <a href="{{ route('admin.agents.index') }}" class="{{ request()->routeIs('admin.agents.*') ? 'active' : '' }}">Manage Agents</a>
            <a href="{{ route('home') }}">Back to Site</a>
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