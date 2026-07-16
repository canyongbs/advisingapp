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

namespace AdvisingApp\Application\Filament\Resources\Applications\Pages;

use AdvisingApp\Application\Actions\DuplicateApplication;
use AdvisingApp\Application\Filament\Resources\Applications\ApplicationResource;
use AdvisingApp\Application\Models\Application;
use AdvisingApp\Application\Models\ApplicationSubmission;
use App\Filament\Tables\Columns\IdColumn;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Actions\ReplicateAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class ListApplications extends ListRecords
{
    protected ?string $heading = 'Online Admissions';

    protected static string $resource = ApplicationResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                $submissionsCountQuery = ApplicationSubmission::query()
                    ->join('applications as version_applications', 'application_submissions.application_id', '=', 'version_applications.id')
                    ->whereColumn('version_applications.root_id', 'applications.root_id')
                    ->selectRaw('count(*)');

                $query->addSelect([
                    'submissions_count' => $submissionsCountQuery,
                ]);
            })
            ->columns([
                IdColumn::make(),
                TextColumn::make('name')
                    ->description(fn (Application $record) => $record->title),
                TextColumn::make('submissions_count')
                    ->label('Submissions'),
                TextColumn::make('created_at')
                    ->label('Created')
                    ->date()
                    ->sortable(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                ReplicateAction::make('Duplicate')
                    ->modalHeading('Duplicate Application')
                    ->excludeAttributes(['submissions_count'])
                    ->mutateRecordDataUsing(function (array $data): array {
                        $data['name'] = "Copy - {$data['name']}";

                        return $data;
                    })
                    ->schema(function (Schema $schema): Schema {
                        return $schema->components([
                            TextInput::make('name')
                                ->label('Name')
                                ->required(),
                        ]);
                    })
                    ->beforeReplicaSaved(function (Model $replica, array $data): void {
                        $replica->name = $data['name'];
                    })
                    ->after(function (Application $replica, Application $record): void {
                        resolve(DuplicateApplication::class, ['original' => $record, 'replica' => $replica])();
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('archiveOrDelete')
                        ->label('Archive / Delete')
                        ->icon('heroicon-o-archive-box')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->modalHeading('Archive or Delete Selected Applications')
                        ->modalDescription('Applications with submissions will be archived. Applications without submissions will be deleted.')
                        ->modalSubmitActionLabel('Confirm')
                        ->action(function (Collection $records): void {
                            $archived = 0;
                            $deleted = 0;

                            /** @var Application $record */
                            foreach ($records as $record) {
                                $hasSubmissions = ApplicationSubmission::query()
                                    ->whereHas(
                                        'submissible',
                                        fn (Builder $query) => $query->withoutGlobalScopes()->where('root_id', $record->root_id),
                                    )
                                    ->exists();

                                if ($hasSubmissions) {
                                    $record->archive();
                                    $archived++;
                                } else {
                                    $record->delete();
                                    $deleted++;
                                }
                            }

                            $parts = [];

                            if ($archived > 0) {
                                $parts[] = "{$archived} " . str('application')->plural($archived) . ' archived';
                            }

                            if ($deleted > 0) {
                                $parts[] = "{$deleted} " . str('application')->plural($deleted) . ' deleted';
                            }

                            Notification::make()
                                ->title(implode(', ', $parts))
                                ->success()
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
