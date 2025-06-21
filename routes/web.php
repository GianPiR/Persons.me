<?php

use Illuminate\Support\Facades\Route;

// Main SPA Route - serve the Vue.js application
Route::get('/', function () {
    return view('app');
});

// Catch-all route for Vue Router (handle client-side routing)
Route::get('/{any}', function () {
    return view('app');
})->where('any', '.*');