<?php

use Assist\Engagement\Http\Controllers\EngagementFileDownloadController;

Route::middleware(['web', 'auth'])->get('/file/{file}/download', EngagementFileDownloadController::class)->name('engagement-file-download');
