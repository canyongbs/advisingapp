<?php

use App\Livewire\EmbedForm;

Route::middleware('web')
    ->prefix('forms')
    ->name('forms.')
    ->group(function () {
        Route::get('/{form}/respond', EmbedForm::class)
            ->name('show');
    });
