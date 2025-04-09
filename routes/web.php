<?php

use App\Http\Controllers\ProjectController;
use App\Http\Controllers\SprintController;

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::get('projects', [ProjectController::class, 'index'])->name('projects.index');
    Route::post('projects', [ProjectController::class, 'store'])->name('projects.store');
    Route::get('projects/{project}', [ProjectController::class, 'show'])->name('projects.show');
    Route::put('projects/{project}', [ProjectController::class, 'update'])->name('projects.update');
    Route::delete('projects/{project}', [ProjectController::class, 'destroy'])->name('projects.destroy');

    Route::get('projects/{project}/sprints/{sprint}', [SprintController::class, 'show'])->name('sprints.show');
    Route::post('sprints', [SprintController::class , 'store'])->name('sprints.store');
    Route::put('/projects/{project}/sprints/{sprint}', [SprintController::class, 'update'])->name('sprints.update');
    Route::delete('projects/{project}/sprints/{sprint}', [SprintController::class, 'destroy'])->name('sprints.destroy');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
