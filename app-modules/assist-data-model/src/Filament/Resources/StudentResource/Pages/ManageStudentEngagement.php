<?php

/*
<COPYRIGHT>

Copyright © 2022-2023, Canyon GBS LLC

All rights reserved.

This file is part of a project developed using Laravel, which is an open-source framework for PHP.
Canyon GBS LLC acknowledges and respects the copyright of Laravel and other open-source
projects used in the development of this solution.

This project is licensed under the Affero General Public License (AGPL) 3.0.
For more details, see https://github.com/canyongbs/assistbycanyongbs/blob/main/LICENSE.

Notice:
- The copyright notice in this file and across all files and applications in this
 repository cannot be removed or altered without violating the terms of the AGPL 3.0 License.
- The software solution, including services, infrastructure, and code, is offered as a
 Software as a Service (SaaS) by Canyon GBS LLC.
- Use of this software implies agreement to the license terms and conditions as stated
 in the AGPL 3.0 License.

For more information or inquiries please visit our website at
https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace Assist\AssistDataModel\Filament\Resources\StudentResource\Pages;

use Illuminate\Database\Eloquent\Model;
use Filament\Resources\Pages\ManageRelatedRecords;
use Assist\AssistDataModel\Filament\Resources\StudentResource;
use Assist\AssistDataModel\Filament\Resources\StudentResource\RelationManagers\EngagementsRelationManager;
use Assist\AssistDataModel\Filament\Resources\StudentResource\RelationManagers\EngagementResponsesRelationManager;

class ManageStudentEngagement extends ManageRelatedRecords
{
    protected static string $resource = StudentResource::class;

    // TODO: Obsolete when there is no table, remove from Filament
    protected static string $relationship = 'engagements';

    protected static ?string $navigationLabel = 'Engagements';

    protected static ?string $breadcrumb = 'Engagements';

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-bottom-center-text';

    public static function canAccess(?Model $record = null): bool
    {
        return (bool) count(static::managers($record));
    }

    public function getRelationManagers(): array
    {
        return static::managers($this->getRecord());
    }

    private static function managers(Model $record): array
    {
        return collect([
            EngagementsRelationManager::class,
            EngagementResponsesRelationManager::class,
        ])
            ->reject(fn ($relationManager) => ! $relationManager::canViewForRecord($record, static::class))
            ->toArray();
    }
}
