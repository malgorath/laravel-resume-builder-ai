<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ResumeController;
use App\Http\Controllers\ResumesController;
use App\Http\Controllers\HomeController;

Route::get('/',[HomeController::class, 'index'])->name('home');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [ProfileController::class, 'dashboard'])->name('dashboard');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Resume Routes
    Route::get('/resumes/upload', [ResumeController::class, 'showUploadForm'])->name('resumes.upload.form');
    Route::post('/resumes/upload', [ResumeController::class, 'upload'])->name('resumes.upload');
    Route::get('/resumes/{id}/download', [ResumeController::class, 'download'])->name('resumes.download');
    
    // List all resumes for the authenticated user
    Route::get('/resumes', [ResumesController::class, 'index'])->name('resumes.index');

    // View a specific resume and trigger analysis if not already done
    Route::get('/resumes/{id}', [ResumesController::class, 'show'])->name('resumes.show');

    // Download a specific resume
    Route::get('/resumes/{id}/download', [ResumesController::class, 'download'])->name('resumes.download');

});



require __DIR__.'/auth.php';
