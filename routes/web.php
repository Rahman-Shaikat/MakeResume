<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ResumeController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => auth()->check()
    ? redirect()->route('dashboard')
    : redirect()->route('login'))->name('home');

Route::middleware('guest')->group(function (): void {
    Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/register', [RegisteredUserController::class, 'store']);
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store']);
});

Route::middleware('auth')->group(function (): void {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

    Route::post('/resume/template', [ResumeController::class, 'selectTemplate'])
        ->name('resume.template.select');
    Route::post('/resume/profile-image', [ResumeController::class, 'uploadProfileImage'])
        ->name('resume.profile-image.store');
    Route::get('/resume/builder', [ResumeController::class, 'builder'])
        ->name('resume.builder');
    Route::put('/resume/builder', [ResumeController::class, 'updateBuilder'])
        ->name('resume.builder.update');
    Route::get('/resume/templates/{template}', [ResumeController::class, 'showTemplate'])
        ->name('resume.templates.show');
});
