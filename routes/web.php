<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ActorController;
use App\Http\Controllers\DirectorController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\MovieController;

Route::get('/', fn() => redirect()->route('movies.index'));

// MOVIES (lista publikus, módosítás login)
Route::get('/movies', [MovieController::class, 'index'])->name('movies.index');

Route::middleware('api.auth')->group(function () {
    Route::get('/movies/create', [MovieController::class, 'create'])->name('movies.create');
    Route::post('/movies', [MovieController::class, 'store'])->name('movies.store');
    Route::get('/movies/{id}/edit', [MovieController::class, 'edit'])->name('movies.edit');
    Route::put('/movies/{id}', [MovieController::class, 'update'])->name('movies.update');
    Route::delete('/movies/{id}', [MovieController::class, 'destroy'])->name('movies.destroy');
});

// CSV/PDF export (lista → export általában publikus lehet, de ha tanár szigorú: tedd api.auth alá)
Route::get('/movies/export/csv', [MovieController::class, 'exportCsv'])->name('movies.export.csv');
Route::get('/movies/export/pdf', [MovieController::class, 'exportPdf'])->name('movies.export.pdf');


// ACTORS
Route::get('/actors', [ActorController::class, 'index'])->name('actors.index');
Route::get('/actors/export/csv', [ActorController::class, 'exportCsv'])->name('actors.export.csv');
Route::get('/actors/export/pdf', [ActorController::class, 'exportPdf'])->name('actors.export.pdf');

Route::middleware('api.auth')->group(function () {
    Route::get('/actors/create', [ActorController::class, 'create'])->name('actors.create');
    Route::post('/actors', [ActorController::class, 'store'])->name('actors.store');
    Route::get('/actors/{id}/edit', [ActorController::class, 'edit'])->name('actors.edit');
    Route::put('/actors/{id}', [ActorController::class, 'update'])->name('actors.update');
    Route::delete('/actors/{id}', [ActorController::class, 'destroy'])->name('actors.destroy');
});


// DIRECTORS
Route::get('/directors', [DirectorController::class, 'index'])->name('directors.index');
Route::get('/directors/export/csv', [DirectorController::class, 'exportCsv'])->name('directors.export.csv');
Route::get('/directors/export/pdf', [DirectorController::class, 'exportPdf'])->name('directors.export.pdf');

Route::middleware('api.auth')->group(function () {
    Route::get('/directors/create', [DirectorController::class, 'create'])->name('directors.create');
    Route::post('/directors', [DirectorController::class, 'store'])->name('directors.store');
    Route::get('/directors/{id}/edit', [DirectorController::class, 'edit'])->name('directors.edit');
    Route::put('/directors/{id}', [DirectorController::class, 'update'])->name('directors.update');
    Route::delete('/directors/{id}', [DirectorController::class, 'destroy'])->name('directors.destroy');
});


// CATEGORIES
Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
Route::get('/categories/export/csv', [CategoryController::class, 'exportCsv'])->name('categories.export.csv');
Route::get('/categories/export/pdf', [CategoryController::class, 'exportPdf'])->name('categories.export.pdf');

Route::middleware('api.auth')->group(function () {
    Route::get('/categories/create', [CategoryController::class, 'create'])->name('categories.create');
    Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::get('/categories/{id}/edit', [CategoryController::class, 'edit'])->name('categories.edit');
    Route::put('/categories/{id}', [CategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{id}', [CategoryController::class, 'destroy'])->name('categories.destroy');
});

require __DIR__.'/auth.php';
