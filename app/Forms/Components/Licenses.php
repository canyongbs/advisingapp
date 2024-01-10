<?php

namespace App\Forms\Components;

use App\Models\User;
use Filament\Forms\Set;
use Filament\Support\Colors\Color;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Placeholder;
use AdvisingApp\Authorization\Enums\LicenseType;

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
                    ->content(fn () => "{$licenseType->getSeatsInUse()} / {$licenseType->getSeats()}"),
                Toggle::make("{$licenseType->value}_enabled")
                    ->hiddenLabel()
                    ->offColor(Color::Red)
                    ->onColor(Color::Green)
                    ->formatStateUsing(function () use ($licenseType) {
                        /** @var User $user */
                        $user = auth()->user();

                        return $user->hasLicense($licenseType);
                    })
                    ->disabled(fn (bool $state) => ! $state && ! $licenseType->hasAvailableLicenses())
                    ->hintIcon(fn (Toggle $component) => $component->isDisabled() ? 'heroicon-m-lock-closed' : null)
                    ->hintIconTooltip("You are out of available {$licenseType->getLabel()} licenses.")
                    ->dehydrateStateUsing(function (bool $state, Set $set) use ($licenseType) {
                        /** @var User $user */
                        $user = auth()->user();

                        $state ? $user->grantLicense($licenseType) : $user->revokeLicense($licenseType);
                    })
                    ->live(),
            ])
            ->columnSpan(1);
    }
}
