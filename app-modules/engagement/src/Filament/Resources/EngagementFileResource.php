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

namespace AdvisingApp\Engagement\Filament\Resources;

use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use AdvisingApp\Engagement\Models\EngagementFile;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use AdvisingApp\Engagement\Filament\Resources\EngagementFileResource\Pages\EditEngagementFile;
use AdvisingApp\Engagement\Filament\Resources\EngagementFileResource\Pages\ViewEngagementFile;
use AdvisingApp\Engagement\Filament\Resources\EngagementFileResource\Pages\ListEngagementFiles;
use AdvisingApp\Engagement\Filament\Resources\EngagementFileResource\Pages\CreateEngagementFile;

class EngagementFileResource extends Resource
{
    protected static ?string $model = EngagementFile::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-duplicate';

    protected static ?string $navigationGroup = 'Engagement Features';

    protected static ?int $navigationSort = 7;

    protected static ?string $navigationLabel = 'Files and Documents';

    protected ?string $heading = 'Files and Documents';

    protected static ?string $modelLabel = 'File or Document';

    protected static ?string $pluralModelLabel = 'Files or Documents';

    // TODO: Look into whether or not we should just delete this resource
    protected static bool $shouldRegisterNavigation = false;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('description')
                    ->required()
                    ->maxLength(255),
                DatePicker::make('retention_date')
                    ->label('Retention Date')
                    ->hintIcon('heroicon-m-question-mark-circle', tooltip: 'The file will be deleted automatically after this date. If left blank, the file will be kept indefinitely.')
                    ->native(false)
                    ->closeOnDateSelection()
                    ->format('Y-m-d')
                    ->minDate(now()->addDay()),
                SpatieMediaLibraryFileUpload::make('file')
                    ->label('File')
                    ->disk('s3')
                    ->collection('file')
                    ->required()
                    ->acceptedFileTypes([
                        'image/png',
                        'image/jpeg',
                        'image/gif',
                        'application/pdf',
                        'application/msword',
                        'text/csv',
                        'application/vnd.ms-excel',
                        'application/msexcel',
                        'application/ms-excel',
                        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                        'application/vnd.ms-powerpoint',
                        'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                        'text/plain',
                        'audio/mpeg',
                        'video/mp4',
                        'application/x-zip-compressed',
                        'application/zip',
                        'application/x-zip',
                    ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListEngagementFiles::route('/'),
            'create' => CreateEngagementFile::route('/create'),
            'view' => ViewEngagementFile::route('/{record}'),
            'edit' => EditEngagementFile::route('/{record}/edit'),
        ];
    }
}
