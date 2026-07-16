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

namespace AdvisingApp\MeetingCenter\Filament\Resources\Events\Pages;

use AdvisingApp\MeetingCenter\Actions\DuplicateEvent;
use AdvisingApp\MeetingCenter\Filament\Resources\Events\EventResource;
use AdvisingApp\MeetingCenter\Models\Event;
use AdvisingApp\MeetingCenter\Models\EventAttendee;
use App\Features\EventArchivingFeature;
use App\Filament\Tables\Columns\IdColumn;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ReplicateAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Livewire\Attributes\Url;

class ListEvents extends ListRecords
{
    protected ?string $heading = 'Event Management';

    protected static string $resource = EventResource::class;

    protected string $view = 'meeting-center::filament.pages.list-events';

    #[Url(as: 'view')]
    public string $viewType = 'table';

    public function setViewType(string $viewType): void
    {
        $this->viewType = $viewType;
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                IdColumn::make(),
                TextColumn::make('title')
                    ->searchable(),
                TextColumn::make('location')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('starts_at')
                    ->label('Starts At')
                    ->dateTime(),
                TextColumn::make('ends_at')
                    ->label('Ends At')
                    ->dateTime(),
                TextColumn::make('createdBy.name')
                    ->label('Created By')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Filter::make('pastEvents')
                    ->label('Hide Past Events')
                    ->query(fn (Builder $query): Builder => $query->where('starts_at', '>=', now()->startOfDay()))
                    ->default(),
            ])
            ->recordActions([
                ViewAction::make(),
                ReplicateAction::make('Duplicate')
                    ->modalHeading('Duplicate Event')
                    ->mutateRecordDataUsing(function (array $data): array {
                        $data['title'] = "Copy - {$data['title']}";

                        return $data;
                    })
                    ->schema(function (Schema $schema): Schema {
                        return $schema->components([
                            TextInput::make('title')
                                ->label('Title')
                                ->required(),
                        ]);
                    })
                    ->beforeReplicaSaved(function (Model $replica, array $data): void {
                        $replica->title = $data['title'];
                    })
                    ->after(function (Event $replica, Event $record): void {
                        resolve(DuplicateEvent::class, ['original' => $record, 'replica' => $replica])();
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    ...(EventArchivingFeature::active() ? [
                        BulkAction::make('archiveOrDelete')
                            ->label('Archive / Delete')
                            ->icon('heroicon-o-archive-box')
                            ->color('danger')
                            ->requiresConfirmation()
                            ->modalHeading('Archive or Delete Selected Events')
                            ->modalDescription('Events with attendees will be archived. Events without attendees will be deleted.')
                            ->modalSubmitActionLabel('Confirm')
                            ->action(function (Collection $records): void {
                                $archived = 0;
                                $deleted = 0;

                                /** @var Event $record */
                                foreach ($records as $record) {
                                    $hasAttendees = EventAttendee::query()
                                        ->where('event_id', $record->id)
                                        ->exists();

                                    if ($hasAttendees) {
                                        $record->archive();
                                        $archived++;
                                    } else {
                                        $record->delete();
                                        $deleted++;
                                    }
                                }

                                $parts = [];

                                if ($archived > 0) {
                                    $parts[] = "{$archived} " . str('event')->plural($archived) . ' archived';
                                }

                                if ($deleted > 0) {
                                    $parts[] = "{$deleted} " . str('event')->plural($deleted) . ' deleted';
                                }

                                Notification::make()
                                    ->title(implode(', ', $parts))
                                    ->success()
                                    ->send();
                            })
                            ->deselectRecordsAfterCompletion(),
                    ] : [DeleteBulkAction::make()
                        ->authorizeIndividualRecords('delete')]),
                ]),
            ])
            ->defaultSort('starts_at');
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
