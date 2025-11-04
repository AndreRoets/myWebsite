<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\AgentController;
use App\Http\Controllers\Admin\AgentAdminController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\UserAdminController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\PropertyAdminController;

Route::get('/', fn() => view('home'))->name('home');

Route::get('/agents', [AgentController::class, 'index'])->name('agents.index');
Route::get('/agents/{agent}', [AgentController::class, 'show'])->name('agents.show'); // New route for agent profile

Route::prefix('properties')->group(function () {
    Route::get('/', [PropertyController::class, 'index'])->name('properties.index');
    Route::get('/search', [PropertyController::class, 'search'])->name('properties.search');
    Route::get('/results', [PropertyController::class, 'results'])->name('properties.results');
    Route::get('/{property:slug}', [PropertyController::class, 'show'])->name('properties.show');
});

Route::post('/properties/{property}/toggle-display', [PropertyController::class, 'toggleDisplay'])
    ->middleware(['auth', 'admin'])
    ->name('properties.toggleDisplay');

Route::get('/contact', fn() => view('contact'))->name('contact');
 
// Temporary debug route to check php.ini settings
Route::get('/php-info', function () {
    phpinfo();
})->name('php.info');

Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/', fn() => redirect()->route('admin.dashboard'));
    Route::get('/dashboard', fn() => view('admin.dashboard'))->name('dashboard');

    // Point the base /admin/properties URL to the create page
    Route::get('properties', [PropertyAdminController::class, 'create'])->name('properties.create');
    // Move the list of properties to /admin/properties/list
    Route::get('properties/list', [PropertyAdminController::class, 'index'])->name('properties.list'); // Legacy route
    Route::resource('properties', PropertyAdminController::class)->except(['show']);

    Route::resource('agents', AgentAdminController::class)->except(['show']);

    // User management routes
    Route::resource('users', UserAdminController::class)->except(['show']);
});

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('login', [LoginController::class, 'login']);

    Route::get('register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('register', [RegisterController::class, 'register']);
});
Route::post('logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// Authenticated user routes
Route::middleware('auth')->group(function () {
    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');

    // Saved Searches Routes
    Route::post('/saved-searches', [App\Http\Controllers\SavedSearchController::class, 'store'])->name('saved-searches.store');
    Route::get('/saved-searches/{savedSearch}/execute', [App\Http\Controllers\SavedSearchController::class, 'execute'])->name('saved-searches.execute');
    Route::delete('/saved-searches/{savedSearch}', [App\Http\Controllers\SavedSearchController::class, 'destroy'])->name('saved-searches.destroy');
});
