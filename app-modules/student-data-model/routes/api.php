<?php

/*
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

    Advising App™ is licensed under the Elastic License 2.0. For more details,
    see https://github.com/canyongbs/advisingapp/blob/main/LICENSE.

    Notice:

    - You may not provide the software to third parties as a hosted or managed
      service, where the service provides users with access to any substantial set of
      the features or functionality of the software.
    - You may not move, change, disable, or circumvent the license key functionality
      in the software, and you may not remove or obscure any functionality in the
      software that is protected by the license key.
    - You may not alter, remove, or obscure any licensing, copyright, or other notices
      of the licensor in the software. Any use of the licensor’s trademarks is subject
      to applicable law.
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS™ and Advising App™ are registered trademarks of
      Canyon GBS LLC, and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS LLC.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

use AdvisingApp\StudentDataModel\Http\Controllers\Api\V1\Students\CreateStudentController;
use AdvisingApp\StudentDataModel\Http\Controllers\Api\V1\Students\DeleteStudentController;
use AdvisingApp\StudentDataModel\Http\Controllers\Api\V1\Students\ListStudentsController;
use AdvisingApp\StudentDataModel\Http\Controllers\Api\V1\Students\StudentEmailAddresses\CreateStudentEmailAddressController;
use AdvisingApp\StudentDataModel\Http\Controllers\Api\V1\Students\StudentEmailAddresses\DeleteStudentEmailAddressController;
use AdvisingApp\StudentDataModel\Http\Controllers\Api\V1\Students\StudentEmailAddresses\UpdateStudentEmailAddressController;
use AdvisingApp\StudentDataModel\Http\Controllers\Api\V1\Students\StudentPhoneNumbers\CreateStudentPhoneNumberController;
use AdvisingApp\StudentDataModel\Http\Controllers\Api\V1\Students\StudentPhoneNumbers\DeleteStudentPhoneNumberController as StudentPhoneNumbersDeleteStudentPhoneNumberController;
use AdvisingApp\StudentDataModel\Http\Controllers\Api\V1\Students\StudentPhoneNumbers\UpdateStudentPhoneNumberController;
use AdvisingApp\StudentDataModel\Http\Controllers\Api\V1\Students\StudentPrograms\ListStudentProgramsController;
use AdvisingApp\StudentDataModel\Http\Controllers\Api\V1\Students\StudentPrograms\StudentProgramsController;
use AdvisingApp\StudentDataModel\Http\Controllers\Api\V1\Students\UpdateStudentController;
use AdvisingApp\StudentDataModel\Http\Controllers\Api\V1\Students\ViewStudentController;
use AdvisingApp\StudentDataModel\Http\Controllers\Api\V1\Students\StudentPrograms\StudentProgramController;
use AdvisingApp\StudentDataModel\Http\Controllers\UpdateStudentInformationSystemSettingsController;
use App\Http\Middleware\CheckOlympusKey;
use Illuminate\Support\Facades\Route;

Route::prefix('api')
    ->middleware([
        'api',
        CheckOlympusKey::class,
    ])
    ->group(function () {
        Route::post('/update-sis-settings', UpdateStudentInformationSystemSettingsController::class)
            ->name('update-sis-settings');
    });

Route::api(majorVersion: 1, routes: function () {
    Route::name('students.')
        ->prefix('students')
        ->group(function () {
            Route::get('/', ListStudentsController::class)->name('index');
            Route::post('/', CreateStudentController::class)->name('create');
            Route::get('{student}', ViewStudentController::class)->name('view');
            Route::patch('{student}', UpdateStudentController::class)->name('update');
            Route::delete('{student}', DeleteStudentController::class)->name('delete');

            Route::name('email-addresses.')
                ->prefix('{student}/email-addresses')
                ->group(function () {
                    Route::post('/', CreateStudentEmailAddressController::class)->name('create');
                    Route::patch('/{studentEmailAddress}', UpdateStudentEmailAddressController::class)->name('update');
                    Route::delete('/{studentEmailAddress}', DeleteStudentEmailAddressController::class)->name('delete');
                });

            Route::name('phone-numbers.')
                ->prefix('{student}/phone-numbers')
                ->group(function () {
                    Route::post('/', CreateStudentPhoneNumberController::class)->name('create');
                    Route::patch('/{studentPhoneNumber}', UpdateStudentPhoneNumberController::class)->name('update');
                    Route::delete('/{studentPhoneNumber}', StudentPhoneNumbersDeleteStudentPhoneNumberController::class)->name('delete');
                });
            Route::get('{student}/programs', ListStudentProgramsController::class)->name('programs.index');
            Route::put('{student}/programs', StudentProgramsController::class)->name('programs.put');
        });
});
