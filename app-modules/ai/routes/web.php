<?php

use Illuminate\Support\Facades\Route;
use AdvisingApp\Ai\Http\Controllers\ShowThreadController;
use AdvisingApp\Ai\Http\Controllers\SendMessageController;

Route::middleware(['web', 'auth'])
    ->name('ai.')
    ->group(function () {
        Route::get('ai/threads/{thread}', ShowThreadController::class)
            ->name('threads.show');

        Route::post('ai/threads/{thread}/messages', SendMessageController::class)
            ->name('threads.messages.send');
    });
