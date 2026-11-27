<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS Inc. All rights reserved.

    Advising App® is licensed under the Elastic License 2.0. For more details,
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
    - Canyon GBS Inc. respects the intellectual property rights of others and expects the
      same in return. Canyon GBS® and Advising App® are registered trademarks of
      Canyon GBS Inc., and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS Inc.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace AdvisingApp\Form\Filament\Resources\Forms\Schemas;

use AdvisingApp\Authorization\Enums\LicenseType;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

/**
 * The settings that decide how people access and submit a form, chosen before the form is built.
 */
class FormAccessSettingsForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Toggle::make('is_authenticated')
                    ->label('Requires authentication')
                    ->live()
                    ->helperText('If enabled, students and prospects must verify their email address before they can open and submit this form. When someone verifies their email, the form will automatically link their submission to their existing student or prospect record.'),
                Toggle::make('generate_prospects')
                    ->label('Generate prospects')
                    ->live()
                    ->helperText("If enabled, the system will check the email address submitted on the form. If it matches an existing student's institutional email address or prospect's primary email address, the form submission will be linked to that record. If no match is found, a new prospect will be created automatically. Forms that generate prospects must include a required Mapped Primary Email Address field and a required Mapped Name field.")
                    ->disabled(fn (): bool => ! auth()->user()?->hasLicense(LicenseType::RecruitmentCrm))
                    ->hintIcon(fn (): ?string => ! auth()->user()?->hasLicense(LicenseType::RecruitmentCrm) ? 'heroicon-m-lock-closed' : null),
            ]);
    }
}
