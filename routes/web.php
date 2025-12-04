<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ResumesController;
use App\Http\Controllers\UserDetailController;
use App\Http\Controllers\UserSkillController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::middleware('auth')->group(function () {
    // Profile Routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Dashboard Route
    Route::get('/dashboard', [ProfileController::class, 'dashboard'])->name('dashboard');

    // User Detail Routes
    Route::get('/user/{id}/details', [UserDetailController::class, 'show'])->name('user.details.show');
    Route::post('/user/{id}/details', [UserDetailController::class, 'update'])->name('user.details.update');
    Route::get('/profile/edit', [UserDetailController::class, 'edit'])->name('profile.details.edit');
    Route::post('/profile/update/{id}', [UserDetailController::class, 'update'])->name('profile.details.update');

    // User Skill Routes
    Route::get('/user/{id}/skills', [UserSkillController::class, 'index'])->name('user.skills.index');
    Route::post('/user/{id}/skills', [UserSkillController::class, 'store'])->name('user.skills.store');
    Route::post('/profile/{id}/skills/add', [UserSkillController::class, 'store'])->name('profile.skills.add');
    Route::post('/profile/skills/confirm', [UserSkillController::class, 'confirmNewSkills'])->name('profile.skills.confirm');
    Route::delete('/profile/skills/{id}', [UserSkillController::class, 'destroy'])->name('profile.skills.delete');
    Route::delete('/skills/{id}', [UserSkillController::class, 'destroy'])->name('skills.destroy');

    // Resume Routes
    Route::get('/resumes/upload', [ResumesController::class, 'showUploadForm'])->name('resumes.upload.form');
    Route::post('/resumes/upload', [ResumesController::class, 'upload'])->name('resumes.upload');
    Route::get('/resumes', [ResumesController::class, 'index'])->name('resumes.index');
    Route::get('/resumes/{id}', [ResumesController::class, 'show'])->name('resumes.show');
    Route::get('/resumes/{id}/download', [ResumesController::class, 'download'])->name('resumes.download');
    Route::get('/resumes/{id}/reset-analysis', [ResumesController::class, 'resetAnalysis'])->name('resumes.resetAnalysis');
    Route::post('/resumes/{id}/match-job', [ResumesController::class, 'matchJob'])->name('resumes.matchJob');
    Route::post('/resumes/{id}/generate-cover-letter', [ResumesController::class, 'generateCoverLetter'])->name('resumes.generateCoverLetter');

    // Job Routes
    Route::resource('jobs', JobController::class);

    // Application Routes
    Route::resource('applications', ApplicationController::class)->except(['edit', 'update']);
    Route::post('/applications/{application}', [ApplicationController::class, 'update'])->name('applications.update');

    // Admin Routes
    Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');
        Route::get('/users', [AdminController::class, 'users'])->name('users.index');
        Route::get('/jobs', [AdminController::class, 'jobs'])->name('jobs.index');
        Route::get('/applications', [AdminController::class, 'applications'])->name('applications.index');
    });
});

require __DIR__.'/auth.php';
