<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Auth\PasswordResetController;
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

Route::get('/forgot-password', [PasswordResetController::class, 'create'])->name('password.request');
Route::post('/forgot-password', [PasswordResetController::class, 'store'])
    ->middleware('throttle:password-reset')
    ->name('password.email');
Route::get('/reset-password/{token}', [PasswordResetController::class, 'edit'])->name('password.reset');
Route::post('/reset-password', [PasswordResetController::class, 'update'])
    ->middleware('throttle:password-reset')
    ->name('password.update');

Route::middleware(['auth', 'administrator'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/program-heads', [DashboardController::class, 'storeProgramHead'])->name('program-heads.store');
});

Route::middleware(['auth', 'registrar'])->prefix('registrar')->name('registrar.')->group(function () {
    Route::get('/', [RegistrarDashboardController::class, 'index'])->name('dashboard');
    Route::view('/enrollment', 'registrar.coming-soon', [
        'feature' => 'Enrollment management',
        'description' => 'Enrollment intake and registration review tools are being prepared.',
    ])->name('enrollment');
    Route::view('/grades', 'registrar.coming-soon', [
        'feature' => 'Grade management',
        'description' => 'A centralized workspace for reviewing and managing student grades is being prepared.',
    ])->name('grades');
    Route::view('/transcript-requests', 'registrar.coming-soon', [
        'feature' => 'Transcript requests',
        'description' => 'Transcript request tracking and processing tools are being prepared.',
    ])->name('transcript-requests');
    Route::view('/academic-calendar', 'registrar.coming-soon', [
        'feature' => 'Academic calendar',
        'description' => 'Important academic dates and registrar deadlines will be available here.',
    ])->name('academic-calendar');
    Route::view('/reports', 'registrar.coming-soon', [
        'feature' => 'Registrar reports',
        'description' => 'Detailed enrollment and records reports are being prepared.',
    ])->name('reports');
    Route::view('/settings', 'registrar.coming-soon', [
        'feature' => 'Registrar settings',
        'description' => 'Registrar workspace preferences and configuration tools are being prepared.',
    ])->name('settings');
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

require __DIR__.'/prospectus-routes.php';
