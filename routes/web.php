<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ResumeController;
use App\Http\Controllers\ResumesController;
use App\Http\Controllers\UserDetailController;
use App\Http\Controllers\UserSkillController;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user/{id}/details', [UserDetailController::class, 'show']);
    Route::post('/user/{id}/details', [UserDetailController::class, 'update']);

    Route::get('/user/{id}/skills', [UserSkillController::class, 'index']);
    Route::post('/user/{id}/skills', [UserSkillController::class, 'store']);
    Route::delete('/skills/{id}', [UserSkillController::class, 'destroy']);
});

use App\Http\Controllers\HomeController;

Route::get('/',[HomeController::class, 'index'])->name('home');

Route::middleware('auth')->group(function () {

    // Profile Routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::post('/profile/skills/confirm', [UserSkillController::class, 'confirmNewSkills'])->name('profile.skills.confirm');

    Route::get('/profile/edit', [UserDetailController::class, 'edit'])->name('profile.edit');
    Route::post('/profile/update/{id}', [UserDetailController::class, 'update'])->name('profile.update');

    Route::post('/profile/{id}/skills/add', [UserSkillController::class, 'store'])->name('profile.skills.add');
    Route::delete('/profile/skills/{id}', [UserSkillController::class, 'destroy'])->name('profile.skills.delete');

    // User Detail Routes
    Route::get('/user/{id}/details', [UserDetailController::class, 'show']);
    Route::post('/user/{id}/details', [UserDetailController::class, 'update']);

    // User Skill Routes
    Route::get('/user/{id}/skills', [UserSkillController::class, 'index']);
    Route::post('/user/{id}/skills', [UserSkillController::class, 'store']);
    Route::delete('/skills/{id}', [UserSkillController::class, 'destroy']);

    // Dashboard Route
    Route::get('/dashboard', [ProfileController::class, 'dashboard'])->name('dashboard');
    
    // Resume Routes
    Route::get('/resumes/upload', [ResumeController::class, 'showUploadForm'])->name('resumes.upload.form');
    Route::post('/resumes/upload', [ResumeController::class, 'upload'])->name('resumes.upload');
    Route::get('/resumes/{id}/download', [ResumeController::class, 'download'])->name('resumes.download');
    Route::get('/resumes/{id}/reset-analysis', [ResumesController::class, 'resetAnalysis'])->name('resumes.resetAnalysis');

    
    // List all resumes for the authenticated user
    Route::get('/resumes', [ResumesController::class, 'index'])->name('resumes.index');

    // View a specific resume and trigger analysis if not already done
    Route::get('/resumes/{id}', [ResumesController::class, 'show'])->name('resumes.show');

    // Download a specific resume
    Route::get('/resumes/{id}/download', [ResumesController::class, 'download'])->name('resumes.download');

});



require __DIR__.'/auth.php';
