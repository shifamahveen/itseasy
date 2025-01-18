<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\Community\CollegeController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
	Route::resource('users', UserController::class);
	Route::resource('colleges', CollegeController::class);
});