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

namespace App\Filament\Resources\UserResource\Actions;

use AdvisingApp\Authorization\Enums\LicenseType;
use AdvisingApp\Authorization\Models\License;
use App\Models\User;
use Closure;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables\Actions\BulkAction;
use Illuminate\Database\Eloquent\Collection;

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
