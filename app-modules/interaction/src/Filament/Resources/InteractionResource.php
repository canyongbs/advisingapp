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

namespace Assist\Interaction\Filament\Resources;

use Filament\Resources\Resource;
use Assist\Interaction\Models\Interaction;
use Assist\Interaction\Filament\Resources\InteractionResource\Pages\EditInteraction;
use Assist\Interaction\Filament\Resources\InteractionResource\Pages\ListInteractions;
use Assist\Interaction\Filament\Resources\InteractionResource\Pages\CreateInteraction;

class InteractionResource extends Resource
{
    protected static ?string $model = Interaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-path-rounded-square';

    protected static ?string $navigationGroup = 'Productivity Tools';

    protected static ?int $navigationSort = 5;

    public static function getRelations(): array
    {
        return [
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListInteractions::route('/'),
            'create' => CreateInteraction::route('/create'),
            'edit' => EditInteraction::route('/{record}/edit'),
        ];
    }
}
