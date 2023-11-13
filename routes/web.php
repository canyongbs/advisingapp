<?php

use App\Models\User;
use Illuminate\Support\Facades\Route;
use App\Notifications\DemoNotification;
use App\Http\Controllers\Imports\DownloadImportFailureCsv;

Route::get('/imports/{import}/failed-rows/download', DownloadImportFailureCsv::class)
    ->name('imports.failed-rows.download')
    ->middleware(['auth']);

Route::get('/form-test', function () {
    return view('form-test');
});

//TODO: remove
Route::get('/demo-notification', function () {
    /** @var User $user */
    $user = auth()->user();

    return (new DemoNotification($user))->toMail(User::first())->render();
})->middleware(['auth']);
