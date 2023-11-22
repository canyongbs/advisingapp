<?php

/*
<COPYRIGHT>

Copyright © 2022-2023, Canyon GBS LLC

All rights reserved.

This file is part of a project developed using Laravel, which is an open-source framework for PHP.
Canyon GBS LLC acknowledges and respects the copyright of Laravel and other open-source
projects used in the development of this solution.

This project is licensed under the Affero General Public License (AGPL) 3.0.
For more details, see https://github.com/canyongbs/assistbycanyongbs/blob/main/LICENSE.

Notice:
- The copyright notice in this file and across all files and applications in this
 repository cannot be removed or altered without violating the terms of the AGPL 3.0 License.
- The software solution, including services, infrastructure, and code, is offered as a
 Software as a Service (SaaS) by Canyon GBS LLC.
- Use of this software implies agreement to the license terms and conditions as stated
 in the AGPL 3.0 License.

For more information or inquiries please visit our website at
https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

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
