<?php

namespace App\Forms\Components;

use App\Models\User;
use Filament\Forms\Components\Grid;
use Filament\Support\Colors\Color;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Fieldset;
use Filament\Notifications\Notification;
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
                    ->content(fn () => "{$licenseType->getAvailableSeats()} / {$licenseType->getSeats()}"),
                Toggle::make("{$licenseType->value}_enabled")
                    ->hiddenLabel()
                    ->offColor(Color::Red)
                    ->onColor(Color::Green)
                    ->formatStateUsing(function (User $record) use ($licenseType) {
                        return $record->hasLicense($licenseType);
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
                    ->disabled(fn (bool $state) => ! $state && ! $licenseType->hasAvailableLicenses())
                    ->hintIcon(fn (Toggle $component, string $operation) => $component->isDisabled() && $operation === 'edit' ? 'heroicon-m-lock-closed' : null)
                    ->hintIconTooltip("You are out of available {$licenseType->getLabel()} licenses.")
                    ->dehydrated(false)
                    ->live(),
            ])
            ->columnSpan(1);
    }
}
