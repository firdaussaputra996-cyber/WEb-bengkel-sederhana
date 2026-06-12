<?php

use App\Http\Controllers\Admin\BookingController as AdminBookingController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\ServiceController as AdminServiceController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\UserDashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', LandingController::class)->name('landing');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.store');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.store');
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::middleware(['auth', 'role:user'])->prefix('user')->name('user.')->group(function () {
    Route::get('/dashboard', [UserDashboardController::class, 'index'])->name('dashboard');
    Route::get('/services', [UserDashboardController::class, 'services'])->name('services');
    Route::get('/booking', [UserDashboardController::class, 'createBooking'])->name('booking.create');
    Route::post('/booking', [UserDashboardController::class, 'storeBooking'])->name('booking.store');
    Route::get('/history', [UserDashboardController::class, 'history'])->name('history');
    Route::get('/profile', [UserDashboardController::class, 'profile'])->name('profile');
    Route::put('/profile', [UserDashboardController::class, 'updateProfile'])->name('profile.update');
});

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', AdminDashboardController::class)->name('dashboard');
    Route::resource('services', AdminServiceController::class)->except(['show']);
    Route::get('bookings/export/{period}', [AdminBookingController::class, 'export'])->name('bookings.export');
    Route::patch('bookings/{booking}/status', [AdminBookingController::class, 'updateStatus'])->name('bookings.status');
    Route::resource('bookings', AdminBookingController::class)->except(['show']);
    Route::get('customers', [CustomerController::class, 'index'])->name('customers.index');
});
