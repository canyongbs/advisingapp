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

namespace AdvisingApp\CaseManagement\Filament\Resources;

use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Clusters\CaseManagementAdministration;
use AdvisingApp\CaseManagement\Models\ChangeRequestType;
use AdvisingApp\CaseManagement\Filament\Resources\ChangeRequestTypeResource\Pages\EditChangeRequestType;
use AdvisingApp\CaseManagement\Filament\Resources\ChangeRequestTypeResource\Pages\ViewChangeRequestType;
use AdvisingApp\CaseManagement\Filament\Resources\ChangeRequestTypeResource\Pages\ListChangeRequestTypes;
use AdvisingApp\CaseManagement\Filament\Resources\ChangeRequestTypeResource\Pages\CreateChangeRequestType;

class ChangeRequestTypeResource extends Resource
{
    protected static ?string $model = ChangeRequestType::class;

    protected static ?string $navigationIcon = 'heroicon-m-rectangle-stack';

    protected static ?int $navigationSort = 40;

    protected static ?string $cluster = CaseManagementAdministration::class;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListChangeRequestTypes::route('/'),
            'create' => CreateChangeRequestType::route('/create'),
            'view' => ViewChangeRequestType::route('/{record}'),
            'edit' => EditChangeRequestType::route('/{record}/edit'),
        ];
    }
}
