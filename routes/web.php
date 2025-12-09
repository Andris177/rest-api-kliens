<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\ActorController;


Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/actors', [ActorController::class, 'index'])->name('actors.index');
Route::get('/actors/create', [ActorController::class, 'create'])->name('actors.create');
Route::post('/actors', [ActorController::class, 'store'])->name('actors.store');
Route::get('/actors/{id}/edit', [ActorController::class, 'edit'])->name('actors.edit');
Route::put('/actors/{id}', [ActorController::class, 'update'])->name('actors.update');
Route::delete('/actors/{id}', [ActorController::class, 'destroy'])->name('actors.destroy');

// Exportok
Route::get('/actors/export/csv', [ActorController::class, 'exportCsv'])->name('actors.export.csv');
Route::get('/actors/export/pdf', [ActorController::class, 'exportPdf'])->name('actors.export.pdf');

require __DIR__.'/auth.php';
