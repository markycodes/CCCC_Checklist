<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Auth\ProgramHeadLoginController;
use App\Http\Controllers\Registrar\DashboardController as RegistrarDashboardController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\StudentCourseCompletionController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'landing')->name('home');

Route::get('/login', [ProgramHeadLoginController::class, 'create'])->name('login');
Route::post('/login', [ProgramHeadLoginController::class, 'store'])
    ->middleware('throttle:program-head-login')
    ->name('login.store');
Route::post('/logout', [ProgramHeadLoginController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

Route::middleware(['auth', 'administrator'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/program-heads', [DashboardController::class, 'storeProgramHead'])->name('program-heads.store');
});

Route::middleware(['auth', 'registrar'])->prefix('registrar')->name('registrar.')->group(function () {
    Route::get('/', [RegistrarDashboardController::class, 'index'])->name('dashboard');
});

Route::middleware(['auth', 'program-head'])->group(function () {
    Route::get('/students', [StudentController::class, 'index'])->name('students.index');
    Route::post('/students', [StudentController::class, 'store'])->name('students.store');
    Route::get('/students/{student}', [StudentController::class, 'show'])->name('students.show');
    Route::put('/students/{student}', [StudentController::class, 'update'])->name('students.update');
    Route::delete('/students/{student}', [StudentController::class, 'destroy'])->name('students.destroy');

    Route::put('/students/{student}/courses/{course}/completion', [StudentCourseCompletionController::class, 'store'])
        ->name('students.courses.complete');
    Route::delete('/students/{student}/courses/{course}/completion', [StudentCourseCompletionController::class, 'destroy'])
        ->name('students.courses.incomplete');

    Route::redirect('/checklist', '/students')->name('checklist');
});
