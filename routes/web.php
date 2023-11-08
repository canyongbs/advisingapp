<?php

use App\Models\User;
use Assist\Task\Models\Task;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Imports\DownloadImportFailureCsv;

Route::get('/imports/{import}/failed-rows/download', DownloadImportFailureCsv::class)
    ->name('imports.failed-rows.download')
    ->middleware(['auth']);

Route::get('form-test', function () {
    return view('form-test');
});

Route::get('/test', function () {
    // dd((new \Assist\Task\Notifications\TaskAssignedToUser(Task::first()))->toMail(User::first()));
    return ((new \Assist\Task\Notifications\TaskAssignedToUser(Task::first()))->toMail(User::first()))->render();
    // dd(new \Assist\Task\Notifications\TaskAssignedToUser(Task::first()));
});
