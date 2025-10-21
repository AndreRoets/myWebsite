<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\AgentController;
use App\Http\Controllers\Admin\AgentAdminController;
use App\Http\Controllers\Admin\PropertyAdminController;

Route::get('/', fn() => view('home'))->name('home');

Route::get('/agents', [AgentController::class, 'index'])->name('agents.index');

Route::prefix('properties')->group(function () {
    Route::get('/', [PropertyController::class, 'index'])->name('properties.index');
    Route::get('/{property:slug}', [PropertyController::class, 'show'])->name('properties.show');
});

Route::get('/contact', fn() => view('contact'))->name('contact');
 
// Temporary debug route to check php.ini settings
Route::get('/php-info', function () {
    phpinfo();
})->name('php.info');

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/', fn() => redirect()->route('admin.dashboard'));
    Route::get('/dashboard', fn() => view('admin.dashboard'))->name('dashboard');

    // Point the base /admin/properties URL to the create page
    Route::get('properties', [PropertyAdminController::class, 'create'])->name('properties.create');
    // Move the list of properties to /admin/properties/list
    Route::get('properties/list', [PropertyAdminController::class, 'index'])->name('properties.list'); // Legacy route
    Route::resource('properties', PropertyAdminController::class)->except(['show']);

    Route::resource('agents', AgentAdminController::class)->except(['show']);
});
