<?php

use Assist\Form\Http\Controllers\FormWidgetController;
use Assist\Form\Http\Middleware\EnsureFormIsEmbeddableAndAuthorized;

Route::prefix('api')
    ->middleware(['api', EnsureFormIsEmbeddableAndAuthorized::class])
    ->group(function () {
        Route::prefix('forms')
            ->name('forms.')
            ->group(function () {
                Route::get('/{form}', [FormWidgetController::class, 'view'])
                    ->name('show');
                Route::post('/{form}/submit', [FormWidgetController::class, 'store'])
                    ->name('submit');
            });
    });
