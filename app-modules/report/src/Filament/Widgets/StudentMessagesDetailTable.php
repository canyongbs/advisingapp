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

namespace AdvisingApp\Report\Filament\Widgets;

use AdvisingApp\Engagement\Enums\EngagementDisplayStatus;
use AdvisingApp\Engagement\Models\Engagement;
use AdvisingApp\Engagement\Models\EngagementResponse;
use AdvisingApp\Engagement\Models\HolisticEngagement;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\Report\Filament\Widgets\Concerns\InteractsWithPageFilters;
use AdvisingApp\StudentDataModel\Models\Student;
use Exception;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\On;

class StudentMessagesDetailTable extends BaseWidget
{
    use InteractsWithPageFilters;

    public string $cacheTag;

    protected static ?string $pollingInterval = null;

    protected static bool $isLazy = false;

    protected static ?string $heading = 'Student Messages';

    protected int | string | array $columnSpan = 'full';

    public function mount(string $cacheTag): void
    {
        $this->cacheTag = $cacheTag;
    }

    #[On('refresh-widgets')]
    public function refreshWidget(): void {}

    public function table(Table $table): Table
    {
        $startDate = $this->getStartDate();
        $endDate = $this->getEndDate();
        $segmentId = $this->getSelectedSegment();

        return $table
            ->query(
                HolisticEngagement::query()
                    ->with(['concern', 'record'])
                    // ->select('sisid', 'full_name', 'primary_email_id')
                    // ->when(
                    //     $startDate && $endDate,
                    //     function (Builder $query) use ($startDate, $endDate): Builder {
                    //         return $query->whereHas(
                    //             'engagements',
                    //             function (Builder $query) use ($startDate, $endDate): Builder {
                    //                 return $query->whereBetween('created_at', [$startDate, $endDate]);
                    //             }
                    //         );
                    //     }
                    // )
                    // ->when(
                    //     $segmentId,
                    //     fn (Builder $query) => $this->segmentFilter($query, $segmentId)
                    // )
                    ->orderBy('created_at', 'desc')
                    ->limit(10)
            )
            // ->paginated(false)
            ->columns([
                TextColumn::make('direction')
                    ->badge()
                    ->getStateUsing(fn (HolisticEngagement $record) => ! is_null($record->record) ? match ($record->record::class) {
                        Engagement::class => 'Outbound',
                        EngagementResponse::class => 'Inbound',
                        default => throw new Exception('Invalid record type'),
                    } : null)
                    ->icon(fn (HolisticEngagement $record) => ! is_null($record->record) ? match ($record->record::class) {
                        Engagement::class => 'heroicon-o-arrow-up-tray',
                        EngagementResponse::class => 'heroicon-o-arrow-down-tray',
                        default => throw new Exception('Invalid record type'),
                    } : null),
                TextColumn::make('status')
                    ->getStateUsing(fn (HolisticEngagement $record) => ! is_null($record->record) ? match ($record->record::class) {
                        EngagementResponse::class => $record->record->status,
                        Engagement::class => EngagementDisplayStatus::getStatus($record->record),
                        default => throw new Exception('Invalid record type'),
                    } : null)
                    ->badge(),
                TextColumn::make('sent_by')
                    ->label('Sent By')
                    ->getStateUsing(function (HolisticEngagement $record): ?string {
                        $related = $record->record;

                        if (is_null($related)) {
                            return null;
                        }

                        return match ($related::class) {
                            Engagement::class => $related->user?->name,

                            EngagementResponse::class => (function () use ($related): string {
                                $sender = $related->sender;

                                assert($sender instanceof Student || $sender instanceof Prospect);

                                return $sender->{$sender->displayNameKey()};
                            })(),

                            default => throw new Exception('Invalid record type'),
                        };
                    }),
            ]);
    }
}
