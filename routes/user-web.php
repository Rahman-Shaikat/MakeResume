<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ResumeBuilderController;
use App\Http\Controllers\ResumeController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function (): void {
    Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/register', [RegisteredUserController::class, 'store']);
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store']);
});

Route::middleware('auth')->group(function (): void {
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

    Route::get('/email/verify', EmailVerificationPromptController::class)
        ->name('verification.notice');
    Route::get('/email/verify/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');
    Route::post('/email/verification-notification', EmailVerificationNotificationController::class)
        ->middleware('throttle:6,1')
        ->name('verification.send');

    Route::middleware('verified')->group(function (): void {
        Route::get('/dashboard', DashboardController::class)->name('dashboard');

        Route::post('/resumes', [ResumeController::class, 'selectTemplate'])
            ->name('resume.template.select');
        Route::delete('/resumes/{resume}', [ResumeController::class, 'destroy'])
            ->name('resume.destroy');
        Route::get('/resume/templates/{template}', [ResumeController::class, 'showTemplate'])
            ->name('resume.templates.show');

        Route::prefix('/resumes/{resume}')->group(function (): void {
            Route::get('/preview', [ResumeController::class, 'showResume'])
                ->name('resume.preview');
            Route::post('/profile-image', [ResumeController::class, 'uploadProfileImage'])
                ->name('resume.profile-image.store');
            Route::delete('/profile-image', [ResumeController::class, 'removeProfileImage'])
                ->name('resume.profile-image.destroy');
            Route::get('/builder', [ResumeController::class, 'builder'])
                ->name('resume.builder');
            Route::patch('/builder/content', [ResumeBuilderController::class, 'updateContent'])
                ->name('resume.builder.content.update');
            Route::post('/builder/sections/reorder', [ResumeBuilderController::class, 'reorderSections'])
                ->name('resume.builder.sections.reorder');
            Route::post('/builder/sections', [ResumeBuilderController::class, 'storeSection'])
                ->name('resume.builder.sections.store');
            Route::patch('/builder/sections/{resumeSection}', [ResumeBuilderController::class, 'updateSection'])
                ->name('resume.builder.sections.update');
            Route::delete('/builder/sections/{resumeSection}', [ResumeBuilderController::class, 'destroySection'])
                ->name('resume.builder.sections.destroy');
            Route::post('/builder/sections/{resumeSection}/items/reorder', [ResumeBuilderController::class, 'reorderItems'])
                ->name('resume.builder.items.reorder');
            Route::post('/builder/sections/{resumeSection}/items', [ResumeBuilderController::class, 'storeItem'])
                ->name('resume.builder.items.store');
            Route::patch('/builder/sections/{resumeSection}/items/{resumeSectionItem}', [ResumeBuilderController::class, 'updateItem'])
                ->name('resume.builder.items.update');
            Route::delete('/builder/sections/{resumeSection}/items/{resumeSectionItem}', [ResumeBuilderController::class, 'destroyItem'])
                ->name('resume.builder.items.destroy');
        });
    });
});
