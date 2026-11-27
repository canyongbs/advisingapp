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

namespace App\Filament\Forms\Components;

use AdvisingApp\Authorization\Enums\LicenseType;
use AdvisingApp\Authorization\Models\License;
use App\Enums\RetentionCrmRestriction;
use App\Models\User;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Text;
use Illuminate\Support\Facades\Auth;

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
                ...array_map(
                    fn (LicenseType $licenseType): Fieldset => $this->generateBlockForLicenseType($licenseType),
                    LicenseType::enabledCases(),
                ),
                Select::make('retention_crm_restriction')
                    ->label('Retention CRM Restriction')
                    ->options(RetentionCrmRestriction::class)
                    ->placeholder('No Restrictions')
                    ->hidden(fn (?User $record): bool => $record?->isAdmin() ?? false)
                    ->columnSpanFull(),
            ]);
    }

    private function generateBlockForLicenseType(LicenseType $licenseType): Fieldset
    {
        return Fieldset::make($licenseType->getLabel())
            ->columns(1)
            ->extraAttributes(['class' => 'grid justify-items-center'])
            ->schema([
                Text::make(fn (): string => "{$licenseType->getAvailableSeats()} / {$licenseType->getSeats()}"),
                Toggle::make("{$licenseType->value}_enabled")
                    ->hiddenLabel()
                    ->offColor('danger')
                    ->onColor('success')
                    ->formatStateUsing(function (?User $record) use ($licenseType): ?bool {
                        return $record?->hasLicense($licenseType);
                    })
                    ->disabled(fn (?bool $state): bool => ! $this->canManageLicenses() || (! $state && ! $licenseType->hasAvailableLicenses()))
                    ->hintIcon(fn (Toggle $component): ?string => $component->isDisabled() ? 'heroicon-m-lock-closed' : null)
                    ->hintIconTooltip(function () use ($licenseType): string {
                        if (! $this->canManageLicenses()) {
                            return 'You do not have permission to change licenses.';
                        }

                        return "You are out of available {$licenseType->getLabel()} licenses.";
                    }),
            ])
            ->columnSpan(1);
    }

    private function canManageLicenses(): bool
    {
        $user = Auth::user();

        return $user instanceof User
            && $user->can('create', License::class);
    }
}
