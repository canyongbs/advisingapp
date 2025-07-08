<?php

use AdvisingApp\Prospect\Http\Controllers\Api\V1\Prospects\ListProspectsController;
use Illuminate\Support\Facades\Route;

Route::api(majorVersion: 1, routes: function () {
    Route::name('prospects.')
        ->prefix('prospects')
        ->group(function () {
            Route::get('/', ListProspectsController::class)->name('index');
            Route::post('/', CreateProspectController::class)->name('create');
            // Route::get('{prospect}', ViewProspectController::class)->name('view');
            // Route::patch('{prospect}', UpdateProspectController::class)->name('update');
            // Route::delete('{prospect}', DeleteProspectController::class)->name('delete');

            // Route::name('email-addresses.')
            //     ->prefix('{prospect}/email-addresses')
            //     ->group(function () {
            //         Route::post('/', CreateProspectEmailAddressController::class)->name('create');
            //         Route::patch('/{prospectEmailAddress}', UpdateProspectEmailAddressController::class)->name('update');
            //         Route::delete('/{prospectEmailAddress}', DeleteProspectEmailAddressController::class)->name('delete');
            //     });
        });
});
