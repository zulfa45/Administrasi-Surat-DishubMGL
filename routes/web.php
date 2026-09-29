<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    $user = auth()->user();
    if ($user->hasRole('admin')) {
        return redirect('/admin/dashboard');
    } elseif ($user->hasRole('staf-loket')) {
        return redirect('/staf/dashboard');
    } elseif ($user->hasRole('karyawan')) {
        return redirect('/karyawan/dashboard');
    }
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Admin Routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard'); // Nanti diganti dengan view admin
    })->name('dashboard');
});

// Staf Loket Routes
Route::middleware(['auth', 'role:staf-loket'])->prefix('staf')->name('staf.')->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard'); // Nanti diganti dengan view staf
    })->name('dashboard');
});

// Karyawan Routes
Route::middleware(['auth', 'role:karyawan'])->prefix('karyawan')->name('karyawan.')->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard'); // Nanti diganti dengan view karyawan
    })->name('dashboard');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
