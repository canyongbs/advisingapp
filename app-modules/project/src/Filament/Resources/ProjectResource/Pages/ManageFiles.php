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

namespace AdvisingApp\Project\Filament\Resources\ProjectResource\Pages;

use AdvisingApp\Project\Filament\Resources\ProjectResource;
use AdvisingApp\Project\Models\ProjectFile;
use App\Features\ProjectFileFeature;
use App\Filament\Tables\Columns\IdColumn;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;

class ManageFiles extends ManageRelatedRecords
{
    protected static string $resource = ProjectResource::class;

    protected static string $relationship = 'files';

    public static function getNavigationLabel(): string
    {
        return 'Files';
    }

    public static function canAccess(array $arguments = []): bool
    {
        $user = auth()->user();

        return ProjectFileFeature::active() && $user->can('viewAny', [ProjectFile::class, $arguments['record']]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('description')
                    ->required()
                    ->maxLength(255),
                DatePicker::make('retention_date')
                    ->label('Retention Date')
                    ->hintIcon('heroicon-m-question-mark-circle', tooltip: 'The file will be deleted automatically after this date. If left blank, the file will be kept indefinitely.')
                    ->closeOnDateSelection()
                    ->minDate(now()->addDay()),
                SpatieMediaLibraryFileUpload::make('file')
                    ->visibility('private')
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

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('description')
            ->columns([
                IdColumn::make(),
                TextColumn::make('description'),
                IconColumn::make('media')
                    ->tooltip(fn ($record) => match ($record->getMedia('file')?->first()?->mime_type) {
                        default => 'File',
                        'image/png' => 'Image (.png)',
                        'image/jpeg' => 'Image (.jpeg)',
                        'image/gif' => 'Image (.gif)',
                        'application/pdf' => 'PDF',
                        'application/msword' => 'Document',
                        'text/csv' => 'CSV',
                        'application/vnd.ms-excel' => 'Spreadsheet',
                        'application/msexcel' => 'Spreadsheet',
                        'application/ms-excel' => 'Spreadsheet',
                        'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'Document',
                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'Spreadsheet',
                        'application/vnd.openxmlformats-officedocument.presentationml.presentation' => 'Presentation',
                        'text/plain' => 'Text File',
                        'audio/mpeg' => 'MP3',
                        'video/mp4' => 'MP4',
                        'application/zip' => 'Zip File'
                    })
                    ->icon(fn ($state) => match ($state->mime_type) {
                        default => 'heroicon-o-paper-clip',
                        'image/png' => 'heroicon-o-photo',
                        'image/jpeg' => 'heroicon-o-camera',
                        'image/gif' => 'heroicon-o-gif',
                        'application/pdf' => 'heroicon-o-document-text',
                        'application/msword' => 'heroicon-o-document-text',
                        'text/csv' => 'heroicon-o-table-cells',
                        'application/vnd.ms-excel' => 'heroicon-o-table-cells',
                        'application/msexcel' => 'heroicon-o-table-cells',
                        'application/ms-excel' => 'heroicon-o-table-cells',
                        'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'heroicon-o-document-text',
                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'heroicon-o-table-cells',
                        'application/vnd.openxmlformats-officedocument.presentationml.presentation' => 'heroicon-o-presentation-chart-bar',
                        'text/plain' => 'heroicon-o-document-text',
                        'audio/mpeg' => 'heroicon-o-musical-note',
                        'video/mp4' => 'heroicon-o-video-camera',
                        'application/zip' => 'heroicon-o-archive-box'
                    }),
                TextColumn::make('created_at')
                    ->label('Date Created')
                    ->dateTime()
                    ->sortable(query: fn ($query, $direction) => $query->orderBy('project_files.created_at', $direction)),
                TextColumn::make('createdBy.name')
                    ->label('Created By')
                    ->sortable(query: fn ($query, $direction) => $query->orderBy('project_files.created_by_id', $direction)),
            ])
            ->headerActions([
                CreateAction::make()
                    ->authorize('create', $this->getOwnerRecord()),
            ])
            ->actions([
                Action::make('download')
                    ->icon('heroicon-o-arrow-down-on-square')
                    ->action(
                        fn (ProjectFile $record) => Storage::disk('s3')
                            ->download(
                                $record
                                    ->getMedia('file')
                                    ->first()
                                    ->getPathRelativeToRoot()
                            )
                    ),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
