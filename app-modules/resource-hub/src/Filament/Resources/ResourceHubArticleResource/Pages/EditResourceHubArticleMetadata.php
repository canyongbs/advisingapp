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

namespace AdvisingApp\ResourceHub\Filament\Resources\ResourceHubArticleResource\Pages;

use AdvisingApp\Division\Models\Division;
use AdvisingApp\ResourceHub\Models\ResourceHubCategory;
use AdvisingApp\ResourceHub\Models\ResourceHubQuality;
use AdvisingApp\ResourceHub\Models\ResourceHubStatus;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;

class EditResourceHubArticleMetadata
{
    public function form(): array
    {
        return [
            Section::make()
                ->schema([
                    Toggle::make('public')
                        ->label('Public')
                        ->default(false)
                        ->onColor('success')
                        ->offColor('gray'),
                    Textarea::make('notes')
                        ->label('Notes')
                        ->columnSpanFull()
                        ->extraInputAttributes(['style' => 'min-height: 12rem;']),
                ]),
            Section::make()
                ->schema([
                    Select::make('quality_id')
                        ->label('Quality')
                        ->relationship('quality', 'name')
                        ->searchable()
                        ->preload()
                        ->exists((new ResourceHubQuality())->getTable(), (new ResourceHubQuality())->getKeyName()),
                    Select::make('status_id')
                        ->label('Status')
                        ->relationship('status', 'name')
                        ->searchable()
                        ->preload()
                        ->exists((new ResourceHubStatus())->getTable(), (new ResourceHubStatus())->getKeyName()),
                    Select::make('category_id')
                        ->label('Category')
                        ->relationship('category', 'name')
                        ->searchable()
                        ->preload()
                        ->exists((new ResourceHubCategory())->getTable(), (new ResourceHubCategory())->getKeyName()),
                    Select::make('division')
                        ->label('Division')
                        ->multiple()
                        ->relationship('division', 'name')
                        ->searchable(['name', 'code'])
                        ->preload()
                        ->exists((new Division())->getTable(), (new Division())->getKeyName()),
                ]),
        ];
    }
}
