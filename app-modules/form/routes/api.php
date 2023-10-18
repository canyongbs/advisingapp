<?php

use Assist\Form\Http\Controllers\FormWidgetController;

Route::prefix('api')
    ->middleware('api')
    ->group(function () {
        Route::prefix('forms')
            ->name('forms.')
            ->group(function () {
                Route::get('/{form}', [FormWidgetController::class, 'view'])
                    ->name('show');
            });
    });
