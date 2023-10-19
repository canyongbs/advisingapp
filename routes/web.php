<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Imports\DownloadImportFailureCsv;

Route::get('/imports/{import}/failed-rows/download', DownloadImportFailureCsv::class)
    ->name('imports.failed-rows.download')
    ->middleware(['auth']);

Route::get('form-test', function () {
    return view('form-test');
});
