<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ResumeBuilderController;
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
    Route::patch('/resume/builder/content', [ResumeBuilderController::class, 'updateContent'])
        ->name('resume.builder.content.update');
    Route::post('/resume/builder/sections/reorder', [ResumeBuilderController::class, 'reorderSections'])
        ->name('resume.builder.sections.reorder');
    Route::post('/resume/builder/sections', [ResumeBuilderController::class, 'storeSection'])
        ->name('resume.builder.sections.store');
    Route::patch('/resume/builder/sections/{resumeSection}', [ResumeBuilderController::class, 'updateSection'])
        ->name('resume.builder.sections.update');
    Route::delete('/resume/builder/sections/{resumeSection}', [ResumeBuilderController::class, 'destroySection'])
        ->name('resume.builder.sections.destroy');
    Route::post('/resume/builder/sections/{resumeSection}/items/reorder', [ResumeBuilderController::class, 'reorderItems'])
        ->name('resume.builder.items.reorder');
    Route::post('/resume/builder/sections/{resumeSection}/items', [ResumeBuilderController::class, 'storeItem'])
        ->name('resume.builder.items.store');
    Route::patch('/resume/builder/sections/{resumeSection}/items/{resumeSectionItem}', [ResumeBuilderController::class, 'updateItem'])
        ->name('resume.builder.items.update');
    Route::delete('/resume/builder/sections/{resumeSection}/items/{resumeSectionItem}', [ResumeBuilderController::class, 'destroyItem'])
        ->name('resume.builder.items.destroy');
    Route::get('/resume/templates/{template}', [ResumeController::class, 'showTemplate'])
        ->name('resume.templates.show');
});
