<?php

use App\Livewire\EmbedForm;
use Assist\Form\Models\Form;
use Illuminate\Http\Request;

Route::middleware('web')
    ->prefix('forms/embed')
    ->name('forms.embed.')
    ->group(function () {
        // Route::get('/{form}', function (Form $form) {
        //     dd($form->loadMissing('items'));
        // })->name('show');

        Route::get('/{embed}', EmbedForm::class)
            ->name('show');

        Route::post('/{form}', function (Request $request, Form $form) {
            ray($request, $form);
        })->name('update');
    });
