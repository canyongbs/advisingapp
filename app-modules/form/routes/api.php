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
                    ->middleware(['signed'])
                    ->name('define');
                Route::post('/{form}/authenticate/request', [FormWidgetController::class, 'requestAuthentication'])
                    ->middleware(['signed'])
                    ->name('request-authentication');
                Route::post('/{form}/authenticate/{authentication}', [FormWidgetController::class, 'authenticate'])
                    ->middleware(['signed'])
                    ->name('authenticate');
                Route::post('/{form}/submit', [FormWidgetController::class, 'store'])
                    ->middleware(['signed'])
                    ->name('submit');
            });
    });
