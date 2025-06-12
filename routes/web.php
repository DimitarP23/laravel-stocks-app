<?php

use Illuminate\Support\Facades\Route;
use App\Models\User;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;


Route::get('/', function () {
    return view('home');
});

Route::get('/about', function () {
    return view('about');
});

// Test route for 500 error
Route::get('/test-500', function () {
    throw new Exception('Test exception for 500 error page');
});

// Authentication routes WITHOUT CSRF (temporary fix)
Route::get('/login', [LoginController::class, 'create'])
    ->middleware('throttle:7,1')
    ->name('login');
Route::post('/login', [LoginController::class, 'store'])
    ->middleware('throttle:7,1');
Route::get('/register', [RegisterController::class, 'create'])->name('register');
Route::post('/register', [RegisterController::class, 'store']);
Route::get('/forgot-password', [ForgotPasswordController::class, 'create'])
    ->name('password.request');
Route::post('/forgot-password', [ForgotPasswordController::class, 'store'])
    ->name('password.email');
Route::get('/reset-password/{token}', [ResetPasswordController::class, 'create'])
    ->name('password.reset');
Route::post('/reset-password', [ResetPasswordController::class, 'store'])
    ->name('password.update');

// Protected routes with CSRF
Route::middleware(['web', 'auth'])->group(function () {
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');

    // Protected routes here
    Route::get('/contact', [ContactController::class, 'show']);
    Route::post('/contact', [ContactController::class, 'submit']);

    // Stocks routes - full CRUD
    Route::resource('stocks', StockController::class);
});
