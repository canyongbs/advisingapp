<?php

namespace App\Filament\Resources\UserResource\Actions;

use Closure;
use App\Models\User;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\MaxWidth;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Checkbox;
use Filament\Tables\Actions\BulkAction;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Collection;
use AdvisingApp\Authorization\Models\License;
use AdvisingApp\Authorization\Enums\LicenseType;

class AssignLicensesBulkAction extends BulkAction
{
    protected array $licenseTypes = [
        LicenseType::ConversationalAi,
        LicenseType::RetentionCrm,
        LicenseType::RecruitmentCrm,
    ];

    protected function setUp(): void
    {
        parent::setUp();

        $this->icon('heroicon-s-wrench-screwdriver')
            ->modalWidth(MaxWidth::Small)
            ->fillForm(fn (Collection $records): array => [
                'records' => $records,
                ...collect($this->licenseTypes)
                    ->mapWithKeys(fn (LicenseType $licenseType): array => [$licenseType->value . '_count' => $licenseType->getAvailableSeats()]),
            ])
            ->form([
                Checkbox::make('replace')
                    ->label('Replace existing licenses?')
                    ->afterStateUpdated(
                        fn (Get $get, Set $set) => collect($this->licenseTypes)
                            ->each(fn (LicenseType $licenseType) => $this->getAfterStateUpdatedCallbackForLicenseType($licenseType)($get, $set))
                    )
                    ->live(),
                ...collect($this->licenseTypes)
                    ->map(fn (LicenseType $licenseType): Toggle => $this->getToggleForLicenseType($licenseType)),
            ])
            ->action(function (array $data, Collection $records) {
                $records->each(function (User $record) use ($data) {
                    collect($this->licenseTypes)->each(function (LicenseType $licenseType) use ($record, $data) {
                        if ($data[$licenseType->value]) {
                            $record->grantLicense($licenseType);
                        } elseif ($data['replace'] && ! $data[$licenseType->value]) {
                            $record->revokeLicense($licenseType);
                        }
                    });
                });

                Notification::make()
                    ->title('Assigned Licenses')
                    ->success()
                    ->send();
            });
    }

    public static function getDefaultName(): ?string
    {
        return 'assign_licenses';
    }

    private function getToggleForLicenseType(LicenseType $licenseType): Toggle
    {
        return Toggle::make($licenseType->value)
            ->label($licenseType->getLabel())
            ->hint(fn (Get $get): string => $get("{$licenseType->value}_count") . ' / ' . $licenseType->getSeats())
            ->hintColor(fn (Get $get): array => $get("{$licenseType->value}_count") > 0 ? Color::Green : Color::Red)
            ->afterStateUpdated($this->getAfterStateUpdatedCallbackForLicenseType($licenseType))
            ->rules([
                fn (Get $get): Closure => function (string $attribute, $value, Closure $fail) use ($licenseType, $get) {
                    if ($get("{$licenseType->value}_count") < 0) {
                        $fail("You do not have enough seats for {$licenseType->getLabel()}");
                    }
                },
            ])
            ->live();
    }

    private function getAfterStateUpdatedCallbackForLicenseType(LicenseType $licenseType): Closure
    {
        return function (Get $get, Set $set) use ($licenseType) {
            $count = $licenseType->getAvailableSeats();

            $existingCount = License::query()
                ->where('type', $licenseType)
                ->whereIn('user_id', $get('records')->pluck('id'))
                ->count();

            if ($get('replace') && ! $get($licenseType->value)) {
                $count += $existingCount;
            } elseif ($get($licenseType->value)) {
                $count += $existingCount - $get('records')->count();
            }

            $set("{$licenseType->value}_count", $count);
        };
    }
}
