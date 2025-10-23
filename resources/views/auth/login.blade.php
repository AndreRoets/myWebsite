@extends('layouts.guest')

@section('title', 'Login')

@section('content')
    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="form-group">
            <label for="email">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus>
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <input id="password" type="password" name="password" required>
        </div>

        {{-- Optional: "Remember Me" functionality --}}
        {{-- <div class="form-group">
            <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
            <label for="remember">Remember Me</label>
        </div> --}}

        <button type="submit" class="btn-submit">Login</button>
    </form>
    <div class="auth-link">
        @if (Route::has('register'))
        <p>Don't have an account? <a href="{{ route('register') }}">Register here</a></p>
        @endif
    </div>
@endsection