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

namespace AdvisingApp\StudentDataModel\Filament\Resources\StudentResource\Pages;

use Filament\Tables\Table;
use Filament\Infolists\Infolist;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use AdvisingApp\Timeline\Models\Timeline;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Contracts\Support\Htmlable;
use AdvisingApp\Engagement\Models\Engagement;
use Filament\Resources\Pages\ManageRelatedRecords;
use AdvisingApp\Engagement\Models\EngagementResponse;
use AdvisingApp\Engagement\Filament\Concerns\EngagementInfolist;
use AdvisingApp\StudentDataModel\Filament\Resources\StudentResource;
use AdvisingApp\Engagement\Filament\Concerns\EngagementResponseInfolist;
use AdvisingApp\Engagement\Filament\Resources\EngagementResource\Components\EngagementViewAction;
use AdvisingApp\Engagement\Filament\Resources\EngagementResponseResource\Components\EngagementResponseViewAction;

class ManageStudentEngagement extends ManageRelatedRecords
{
    use EngagementInfolist;
    use EngagementResponseInfolist;

    protected static string $resource = StudentResource::class;

    // TODO: Obsolete when there is no table, remove from Filament
    protected static string $relationship = 'timeline';

    protected static ?string $navigationLabel = 'Email and Texts';

    protected static ?string $breadcrumb = 'Email and Texts';

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-bottom-center-text';

    public function getTitle(): string | Htmlable
    {
        return 'Manage Student Email and Texts';
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema(fn (Timeline $record) => match ($record->timelineable::class) {
            Engagement::class => $this->engagementInfolist(),
            EngagementResponse::class => $this->engagementResponseInfolist(),
        });
    }

    public function table(Table $table): Table
    {
        return $table
            ->defaultSort('record_sortable_date', 'desc')
            ->modifyQueryUsing(fn (Builder $query) => $query->whereHasMorph('timelineable', [
                Engagement::class,
                EngagementResponse::class,
            ]))
            ->columns([
                TextColumn::make('direction')
                    ->getStateUsing(fn (Timeline $record) => match ($record->timelineable::class) {
                        Engagement::class => 'Outbound',
                        EngagementResponse::class => 'Inbound',
                    })
                    ->icon(fn (string $state) => match ($state) {
                        'Outbound' => 'heroicon-o-arrow-up-tray',
                        'Inbound' => 'heroicon-o-arrow-down-tray',
                    }),
                TextColumn::make('type')
                    ->getStateUsing(fn (Timeline $record) => $record->timelineable->getDeliveryMethod()),
                // TextColumn::make('timelineable')
                //     ->label('Type')
                //     ->sortable(),
                // TextColumn::make('timelineable_id')
                //     ->label('ID')
                //     ->sortable(),
                TextColumn::make('record_sortable_date')
                    ->label('Date')
                    ->sortable(),
            ])
            ->actions([
                ViewAction::make()
                    ->modalHeading(fn (Timeline $record) => 'View ' . $record->timelineable->getDeliveryMethod()->getLabel()),
                // ->modalContent(fn (Timeline $record) => $record->timelineable),
                // ->record(fn (Timeline $record) => $record->entity),
                // EngagementViewAction::make(),
                // EngagementResponseViewAction::make(),
            ]);
    }
}
