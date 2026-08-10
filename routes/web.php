<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\IndexController;

Route::get('/', IndexController::class)->name('index');

/**
 * Authentication route file
 */
require __DIR__ . '/auth.php';

/**
 * Dashboard route file
 */
require __DIR__ . '/dashboard.php';
