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

namespace AdvisingApp\Interaction\Enums;

use AdvisingApp\Interaction\Filament\Resources\InteractionDrivers\InteractionDriverResource;
use AdvisingApp\Interaction\Filament\Resources\InteractionInitiatives\InteractionInitiativeResource;
use AdvisingApp\Interaction\Filament\Resources\InteractionOutcomes\InteractionOutcomeResource;
use AdvisingApp\Interaction\Filament\Resources\InteractionRelations\InteractionRelationResource;
use AdvisingApp\Interaction\Filament\Resources\InteractionStatuses\InteractionStatusResource;
use AdvisingApp\Interaction\Filament\Resources\InteractionTypes\InteractionTypeResource;
use Filament\Resources\Resource;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Support\Str;

enum InteractionManagementTab: string implements HasLabel
{
    case Initiatives = 'initiatives';

    case Drivers = 'drivers';

    case Outcomes = 'outcomes';

    case Relations = 'relations';

    case Statuses = 'statuses';

    case Types = 'types';

    public function canView(): bool
    {
        return $this->getResource()::canViewAny();
    }

    public function getLabel(): string
    {
        return match ($this) {
            self::Initiatives => 'Initiatives',
            self::Drivers => 'Drivers',
            self::Outcomes => 'Outcomes',
            self::Relations => 'Relations',
            self::Statuses => 'Statuses',
            self::Types => 'Types',
        };
    }

    public function getSingularLabel(): string
    {
        return match ($this) {
            self::Initiatives => 'Initiative',
            self::Drivers => 'Driver',
            self::Outcomes => 'Outcome',
            self::Relations => 'Relation',
            self::Statuses => 'Status',
            self::Types => 'Type',
        };
    }

    /**
     * @return class-string<Resource>
     */
    public function getResource(): string
    {
        return match ($this) {
            self::Initiatives => InteractionInitiativeResource::class,
            self::Drivers => InteractionDriverResource::class,
            self::Outcomes => InteractionOutcomeResource::class,
            self::Relations => InteractionRelationResource::class,
            self::Statuses => InteractionStatusResource::class,
            self::Types => InteractionTypeResource::class,
        };
    }

    /**
     * The property name used by the `InteractionManagementSettings` class, e.g. `is_driver_enabled`.
     */
    public function getSettingName(string $suffix): string
    {
        return 'is_' . Str::lower($this->getSingularLabel()) . '_' . $suffix;
    }
}
