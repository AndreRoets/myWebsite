<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\Admin\PropertyAdminController;

Route::get('/', fn() => view('home'))->name('home');

Route::prefix('properties')->group(function () {
    Route::get('/', [PropertyController::class, 'index'])->name('properties.index');
    Route::get('/{property:slug}', [PropertyController::class, 'show'])->name('properties.show');
});
 
Route::prefix('admin')->name('admin.')->group(function () {
    // Point the base /admin/properties URL to the create page
    Route::get('properties', [PropertyAdminController::class, 'create'])->name('properties.create');
    // Move the list of properties to /admin/properties/list
    Route::get('properties/list', [PropertyAdminController::class, 'index'])->name('properties.list');
    Route::resource('properties', PropertyAdminController::class)->except(['show', 'index', 'create']);
});
