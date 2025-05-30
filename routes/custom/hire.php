<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Hire\JobController;
use App\Http\Controllers\Hire\ApplicantController;
use App\Http\Controllers\Hire\ResumeController;
use App\Http\Controllers\Hire\JobApplicationController;
use App\Http\Middleware\Login;

/* Auth Routes */
Route::resource('/admin/job', JobController::class);
Route::get('job', [JobController::class, 'publicindex'])->name('job.publicindex');
Route::get('job/{job}/apply', [JobApplicationController::class, 'apply'])->name('job.applyForm');
Route::post('job/{job}/apply', [JobApplicationController::class, 'storeApplication'])
    ->name('job.apply')
    ->middleware('auth');

Route::get('/admin/job/{job}/applications', [JobApplicationController::class, 'applications'])->name('job.applications');
Route::post('/job/parse-resume', [JobApplicationController::class, 'parseResume'])->name('job.parseResume');

Route::post('job/{job}/bookmark', [JobController::class, 'bookmark'])->name('job.bookmark');

Route::post('/resume/parse', [JobApplicationController::class, 'parse'])->name('resume.parse');
Route::post('/parse-resume', [JobApplicationController::class, 'parseResume'])->name('parse.resume');