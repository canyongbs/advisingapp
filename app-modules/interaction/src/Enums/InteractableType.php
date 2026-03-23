<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS LLC. All rights reserved.

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

namespace AdvisingApp\Interaction\Enums;

use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\StudentDataModel\Models\Student;
use App\Models\User;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Database\Eloquent\Builder;

enum InteractableType: string implements HasLabel
{
    case Prospect = 'prospect';

    case Student = 'student';

    public function getLabel(): string
    {
        return $this->name;
    }

    /**
     * @return array<string, string>
     */
    public static function getLicensedOptions(User $user): array
    {
        return collect(static::cases())
            ->filter(fn (self $type): bool => match ($type) {
                self::Prospect => $user->hasLicense(Prospect::getLicenseType()),
                self::Student => $user->hasLicense(Student::getLicenseType()),
            })
            ->mapWithKeys(fn (self $type): array => [$type->value => $type->getLabel()])
            ->toArray();
    }

    public static function filterQueryByLicense(Builder $query, User $user): Builder
    {
        return $query
            ->when(
                ! $user->hasLicense(Prospect::getLicenseType()),
                fn (Builder $query) => $query->where('interactable_type', '!=', self::Prospect->value),
            )
            ->when(
                ! $user->hasLicense(Student::getLicenseType()),
                fn (Builder $query) => $query->where('interactable_type', '!=', self::Student->value),
            );
    }
}
