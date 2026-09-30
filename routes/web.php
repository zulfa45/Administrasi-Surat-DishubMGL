<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

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

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\DepartmentController;
use App\Http\Controllers\IncomingLetterController;

// Surat Masuk Routes
Route::middleware(['auth'])->group(function () {
    Route::get('surat-masuk/{surat_masuk}/file', [IncomingLetterController::class, 'previewFile'])->name('surat-masuk.file');
    Route::get('surat-masuk/{surat_masuk}', [IncomingLetterController::class, 'show'])->name('surat-masuk.show');
    Route::resource('surat-masuk', IncomingLetterController::class)->except(['show'])->middleware('role:admin|staf-loket');
});

// Admin Routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('users', UserController::class);
    Route::resource('departments', DepartmentController::class);
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
