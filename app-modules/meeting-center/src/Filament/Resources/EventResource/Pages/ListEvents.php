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

namespace AdvisingApp\MeetingCenter\Filament\Resources\EventResource\Pages;

use AdvisingApp\MeetingCenter\Actions\DuplicateEvent;
use AdvisingApp\MeetingCenter\Filament\Resources\EventResource;
use AdvisingApp\MeetingCenter\Models\Event;
use App\Filament\Tables\Columns\IdColumn;
use App\Filament\Tables\Columns\OpenSearch\TextColumn;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ReplicateAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Livewire\Attributes\Url;

class ListEvents extends ListRecords
{
    protected ?string $heading = 'Event Management';

    protected static string $resource = EventResource::class;

    protected static string $view = 'meeting-center::filament.pages.list-events';

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
                    ->sortable(),
                TextColumn::make('starts_at')
                    ->sortable(),
                TextColumn::make('ends_at')
                    ->sortable(),
            ])
            ->filters([
                Filter::make('pastEvents')
                    ->label('Hide Past Events')
                    ->query(fn (Builder $query): Builder => $query->where('starts_at', '>=', now()->startOfDay()))
                    ->default(),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
                ReplicateAction::make('Duplicate')
                    ->label('Duplicate')
                    ->modalHeading('Duplicate Event')
                    ->modalSubmitActionLabel('Duplicate')
                    ->mutateRecordDataUsing(function (array $data): array {
                        $data['title'] = "Copy - {$data['title']}";

                        return $data;
                    })
                    ->form(function (Form $form): Form {
                        return $form->schema([
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
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
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
