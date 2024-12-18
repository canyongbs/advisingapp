<?php

/*
<COPYRIGHT>

    Copyright © 2016-2024, Canyon GBS LLC. All rights reserved.

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

namespace App\Filament\Forms\Components;

use AdvisingApp\Authorization\Enums\LicenseType;
use AdvisingApp\Authorization\Models\License;
use App\Models\User;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Support\Colors\Color;

class Licenses extends Section
{
    public function setUp(): void
    {
        parent::setUp();

        $this->heading('License Management')
            ->columnSpanFull()
            ->columns([
                'md' => 3,
            ])
            ->collapsible()
            ->schema([
                $this->generateBlockForLicenseType(LicenseType::ConversationalAi),
                $this->generateBlockForLicenseType(LicenseType::RetentionCrm),
                $this->generateBlockForLicenseType(LicenseType::RecruitmentCrm),
            ]);
    }

    private function generateBlockForLicenseType(LicenseType $licenseType): Fieldset
    {
        return Fieldset::make($licenseType->getLabel())
            ->columns(1)
            ->extraAttributes(['class' => 'grid justify-items-center'])
            ->schema([
                Placeholder::make("{$licenseType->value}_count")
                    ->extraAttributes(['class' => 'grid justify-items-center'])
                    ->hiddenLabel()
                    ->content(fn () => "{$licenseType->getAvailableSeats()} / {$licenseType->getSeats()}"),
                Toggle::make("{$licenseType->value}_enabled")
                    ->hiddenLabel()
                    ->offColor(Color::Red)
                    ->onColor(Color::Green)
                    ->formatStateUsing(function (?User $record) use ($licenseType) {
                        return $record?->hasLicense($licenseType);
                    })
                    ->afterStateUpdated(function (bool $state, User $record) use ($licenseType) {
                        $notification = Notification::make();

                        if ($state) {
                            $record->grantLicense($licenseType);
                            $notification->title("Granted license for {$licenseType->getLabel()}")
                                ->success();
                        } else {
                            $record->revokeLicense($licenseType);
                            $notification->title("Revoked license for {$licenseType->getLabel()}")
                                ->danger();
                        }

                        $notification->send();
                    })
                    ->disabled(fn (?bool $state) => ! $state && ! $licenseType->hasAvailableLicenses())
                    ->hintIcon(fn (Toggle $component) => $component->isDisabled() ? 'heroicon-m-lock-closed' : null)
                    ->hintIconTooltip(function () use ($licenseType) {
                        /** @var User $user */
                        $user = auth()->user();

                        if ($user->cannot('create', License::class)) {
                            return 'You do not have permission to change licenses.';
                        }

                        return "You are out of available {$licenseType->getLabel()} licenses.";
                    })
                    ->dehydrated(false)
                    ->live(),
            ])
            ->columnSpan(1);
    }
}
