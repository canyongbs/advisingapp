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

namespace Assist\Application\Filament\Resources;

use Filament\Resources\Resource;
use Filament\Resources\Pages\Page;
use Illuminate\Database\Eloquent\Builder;
use Assist\Application\Models\Application;
use Assist\Application\Filament\Resources\ApplicationResource\Pages\EditApplication;
use Assist\Application\Filament\Resources\ApplicationResource\Pages\ListApplications;
use Assist\Application\Filament\Resources\ApplicationResource\Pages\CreateApplication;
use Assist\Application\Filament\Resources\ApplicationResource\Pages\ManageApplicationSubmissions;

class ApplicationResource extends Resource
{
    protected static ?string $model = Application::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Forms and Surveys';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'Manage Admissions';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['fields']);
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            EditApplication::class,
            ManageApplicationSubmissions::class,
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListApplications::route('/'),
            'create' => CreateApplication::route('/create'),
            'edit' => EditApplication::route('/{record}/edit'),
            'manage-submissions' => ManageApplicationSubmissions::route('/{record}/submissions'),
        ];
    }
}
