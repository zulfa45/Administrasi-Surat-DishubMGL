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
use App\Http\Controllers\DispositionController;
use App\Http\Controllers\ReportController;

// Surat Masuk & Disposisi Routes
Route::middleware(['auth'])->group(function () {
    Route::get('surat-masuk/{surat_masuk}/file', [IncomingLetterController::class, 'previewFile'])->name('surat-masuk.file');
    Route::get('surat-masuk/{surat_masuk}/disposisi-pdf', [ReportController::class, 'printDispositionPdf'])->name('surat-masuk.disposisi-pdf');
    Route::get('surat-masuk/{surat_masuk}', [IncomingLetterController::class, 'show'])->name('surat-masuk.show');
    Route::resource('surat-masuk', IncomingLetterController::class)->except(['show'])->middleware('role:admin|staf-loket');

    // Disposisi Routes (Tahap U-07 & U-08)
    Route::get('surat-masuk/{surat_masuk}/disposisi', [DispositionController::class, 'create'])->name('disposisi.create')->middleware('role:admin|staf-loket');
    Route::post('surat-masuk/{surat_masuk}/disposisi', [DispositionController::class, 'store'])->name('disposisi.store')->middleware('role:admin|staf-loket');
    Route::delete('disposisi/{assignment}', [DispositionController::class, 'destroy'])->name('disposisi.destroy')->middleware('role:admin|staf-loket');
    Route::patch('disposisi/{assignment}/status', [DispositionController::class, 'updateStatus'])->name('disposisi.status');
});

// Laporan PDF Routes (Tahap U-14)
Route::middleware(['auth', 'role:admin|staf-loket'])->prefix('laporan')->name('laporan.')->group(function () {
    Route::get('/', [ReportController::class, 'index'])->name('index');
    Route::get('/pdf', [ReportController::class, 'printPdf'])->name('pdf');
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

use App\Http\Controllers\Karyawan\TaskController;

// Karyawan Routes (Tahap U-09, U-10, U-11)
Route::middleware(['auth', 'role:karyawan'])->prefix('karyawan')->name('karyawan.')->group(function () {
    Route::get('/dashboard', [TaskController::class, 'dashboard'])->name('dashboard');
    Route::get('/tasks', [TaskController::class, 'index'])->name('tasks.index');
    Route::get('/tasks/{task}', [TaskController::class, 'show'])->name('tasks.show');
    Route::patch('/tasks/{task}/status', [TaskController::class, 'updateStatus'])->name('tasks.status');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
