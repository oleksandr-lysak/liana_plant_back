<?php

use App\Http\Controllers\Api\V1\ProfileController;
use App\Http\Controllers\Web\MasterController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', [MasterController::class, 'index'])->name('welcome');
Route::get('/masters', [MasterController::class, 'fetchMasters'])->name('masters.fetch');
Route::get('/masters/{master}', [MasterController::class, 'show'])->name('masters.show');

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/locale/{locale}', function ($locale) {
    session(['locale' => $locale]);
    return back();
});

require __DIR__.'/auth.php';
