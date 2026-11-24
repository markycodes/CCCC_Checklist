<?php

use App\Http\Controllers\ProspectusController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'program-head'])->group(function () {
    Route::get('/prospectus', [ProspectusController::class, 'show'])->name('prospectus.show');
    Route::get('/prospectus/download', [ProspectusController::class, 'download'])->name('prospectus.download');
    Route::get('/prospectus/pdf', [ProspectusController::class, 'stream'])->name('prospectus.stream');
});
