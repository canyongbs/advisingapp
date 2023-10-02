<?php

use App\Livewire\RenderForm;

Route::middleware('web')
    ->prefix('forms')
    ->name('forms.')
    ->group(function () {
        Route::get('/{form}/respond', RenderForm::class)
            ->name('show');
    });
