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

namespace App\Filament\Pages;

use AdvisingApp\Authorization\Enums\LicenseType;
use App\Models\User;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

/**
 * @property Schema $form
 */
class WorkingHours extends ProfilePage
{
    protected static ?string $slug = 'working-hours';

    protected static ?string $title = 'Working Hours';

    protected static ?int $navigationSort = 80;

    public function form(Schema $schema): Schema
    {
        /** @var User $user */
        $user = auth()->user();
        $hasCrmLicense = $user->hasAnyLicense([LicenseType::RetentionCrm, LicenseType::RecruitmentCrm]);

        return $schema
            ->components([
                Section::make('Working Hours')
                    ->visible($hasCrmLicense)
                    ->schema([
                        Toggle::make('working_hours_are_enabled')
                            ->label('Set Working Hours')
                            ->live()
                            ->hint(fn (Get $get): string => $get('are_working_hours_visible_on_profile') ? 'Visible on profile' : 'Not visible on profile'),
                        Checkbox::make('are_working_hours_visible_on_profile')
                            ->label('Show Working Hours on profile')
                            ->visible(fn (Get $get) => $get('working_hours_are_enabled'))
                            ->live(),
                        Section::make('Days')
                            ->schema($this->getHoursForDays('working_hours'))
                            ->visible(fn (Get $get) => $get('working_hours_are_enabled')),
                    ]),
            ]);
    }
}
