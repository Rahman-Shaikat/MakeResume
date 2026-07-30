<?php

use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

Route::view('/about-us', 'pages.about')->name('about');
Route::view('/contact-us', 'pages.contact')->name('contact');
Route::view('/pricing', 'pages.pricing')->name('pricing');
Route::view('/privacy-policy', 'pages.privacy')->name('privacy');
Route::view('/terms-of-service', 'pages.terms')->name('terms');

Route::get('/', HomeController::class)->name('home');
