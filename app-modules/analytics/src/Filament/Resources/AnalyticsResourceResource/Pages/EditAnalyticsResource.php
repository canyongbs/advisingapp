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
    - Test

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace AdvisingApp\Analytics\Filament\Resources\AnalyticsResourceResource\Pages;

use Filament\Forms\Form;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\EditRecord;
use AdvisingApp\Analytics\Models\AnalyticsResourceCategory;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use AdvisingApp\Analytics\Filament\Resources\AnalyticsResourceResource;

class EditAnalyticsResource extends EditRecord
{
    protected static string $resource = AnalyticsResourceResource::class;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->columns()
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->string()
                            ->unique(ignoreRecord: true),
                        Textarea::make('description')
                            ->nullable()
                            ->string(),
                        Select::make('category_id')
                            ->relationship('category', 'name')
                            ->required()
                            ->live()
                            ->helperText(fn ($state) => AnalyticsResourceCategory::find($state)?->description),
                        Select::make('source_id')
                            ->relationship('source', 'name'),
                        TextInput::make('url')
                            ->nullable()
                            ->url(),
                        SpatieMediaLibraryFileUpload::make('thumbnail')
                            ->nullable(),
                        Checkbox::make('is_active')
                            ->label('Active'),
                        Checkbox::make('is_included_in_data_portal')
                            ->label('Included in Data Portal'),
                    ]),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
