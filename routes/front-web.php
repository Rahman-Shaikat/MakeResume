<?php

use App\Http\Controllers\ContactMessageController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PublicTemplatePreviewController;
use Illuminate\Support\Facades\Route;

Route::view('/about-us', 'pages.about')->name('about');
Route::view('/contact-us', 'pages.contact')->name('contact');
Route::post('/contact-us', ContactMessageController::class)
    ->middleware('throttle:contact-form')
    ->name('contact.store');
Route::view('/pricing', 'pages.pricing')->name('pricing');
Route::view('/privacy-policy', 'pages.privacy')->name('privacy');
Route::view('/terms-of-service', 'pages.terms')->name('terms');
Route::get('/template-previews/{template}', PublicTemplatePreviewController::class)
    ->where('template', '[a-z0-9]+(?:-[a-z0-9]+)*')
    ->name('home.template-preview');

Route::get('/', HomeController::class)->name('home');
