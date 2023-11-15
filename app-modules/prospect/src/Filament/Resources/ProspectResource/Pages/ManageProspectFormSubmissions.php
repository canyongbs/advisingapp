<?php

namespace Assist\Prospect\Filament\Resources\ProspectResource\Pages;

use Filament\Tables\Table;
use App\Filament\Columns\IdColumn;
use Assist\Prospect\Models\Prospect;
use Illuminate\Support\Facades\Cache;
use Assist\Form\Models\FormSubmission;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Assist\Form\Filament\Resources\FormResource;
use Filament\Resources\Pages\ManageRelatedRecords;
use Assist\Prospect\Filament\Resources\ProspectResource;

class ManageProspectFormSubmissions extends ManageRelatedRecords
{
    protected static string $resource = ProspectResource::class;

    protected static string $relationship = 'formSubmissions';

    // TODO: Automatically set from Filament based on relationship name
    protected static ?string $navigationLabel = 'Form Submissions';

    // TODO: Automatically set from Filament based on relationship name
    protected static ?string $breadcrumb = 'Form Submissions';

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                IdColumn::make(),
                TextColumn::make('form.name')
                    ->searchable()
                    ->url(fn (FormSubmission $record): string => FormResource::getUrl('edit', ['record' => $record->form])),
                TextColumn::make('created_at')
                    ->sortable(),
            ])
            ->filters([
            ])
            ->actions([
                ViewAction::make()
                    ->modalHeading(fn (FormSubmission $record) => "Submission Details: {$record->created_at}")
                    ->modalContent(fn (FormSubmission $record) => view('form::submission', ['submission' => $record])),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getNavigationItems(array $urlParameters = []): array
    {
        $item = parent::getNavigationItems($urlParameters)[0];

        $ownerRecord = $urlParameters['record'];

        /** @var Prospect $ownerRecord */
        $formSubmissionsCount = Cache::tags('form-submission-count')
            ->remember(
                "form-submission-count-{$ownerRecord->getKey()}",
                now()->addMinutes(5),
                function () use ($ownerRecord): int {
                    return $ownerRecord->formSubmissions()->count();
                },
            );

        $item->badge($formSubmissionsCount > 0 ? $formSubmissionsCount : null);

        return [$item];
    }
}
