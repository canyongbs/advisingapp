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

use Assist\Authorization\Http\Controllers\SocialiteController;
use Assist\Authorization\Http\Controllers\Auth\LogoutController;
use Assist\Authorization\Http\Controllers\Auth\OneTimeLoginController;

Route::middleware('web')->group(function () {
    Route::prefix('auth')->name('socialite.')->group(function () {
        Route::get('/{provider}/redirect', [SocialiteController::class, 'redirect'])
            ->name('redirect');

        Route::get('/{provider}/callback', [SocialiteController::class, 'callback'])
            ->name('callback');
    });

    Route::get('/auth/login/{user}', OneTimeLoginController::class)
        ->name('login.one-time')
        ->middleware('signed');

    Route::post('/auth/logout', LogoutController::class)
        ->name('logout');
});
