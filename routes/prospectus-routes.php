<?php

use App\Http\Controllers\ProspectusController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'registrar'])->group(function () {
    Route::get('/prospectus', [ProspectusController::class, 'show'])->name('prospectus.show');
    Route::get('/prospectus/download', [ProspectusController::class, 'download'])->name('prospectus.download');
    Route::get('/prospectus/pdf', [ProspectusController::class, 'stream'])->name('prospectus.stream');

    Route::get('/students/{student}/prospectus', [ProspectusController::class, 'show'])
        ->name('students.prospectus.show');
    Route::get('/students/{student}/prospectus/download', [ProspectusController::class, 'download'])
        ->name('students.prospectus.download');
    Route::get('/students/{student}/prospectus/pdf', [ProspectusController::class, 'stream'])
        ->name('students.prospectus.stream');
});
