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

use AdvisingApp\Campaign\Filament\Resources\Campaigns\CampaignResource;
use AdvisingApp\Engagement\Enums\EngagementDisplayStatus;
use AdvisingApp\Engagement\Enums\EngagementResponseType;
use AdvisingApp\Engagement\Models\Engagement;
use AdvisingApp\Engagement\Models\EngagementResponse;
use AdvisingApp\Engagement\Models\HolisticEngagement;
use AdvisingApp\Notification\Enums\NotificationChannel;
use AdvisingApp\Prospect\Filament\Resources\Prospects\ProspectResource;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\Report\Filament\Exports\StudentMessagesDetailTableExporter;
use AdvisingApp\Report\Filament\Widgets\Concerns\InteractsWithPageFilters;
use AdvisingApp\StudentDataModel\Filament\Resources\Students\StudentResource;
use AdvisingApp\StudentDataModel\Models\Student;
use App\Models\User;
use Exception;
use Filament\Actions\ExportAction;
use Filament\Actions\Exports\Enums\ExportFormat;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
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
        $groupId = $this->getSelectedGroup();

        return $table
            ->recordTitleAttribute('record_id')
            ->defaultSort('record_sortable_date', 'desc')
            ->query(
                HolisticEngagement::query()
                    ->with(['concern', 'record'])
                    ->when(
                        $startDate && $endDate,
                        function (Builder $query) use ($startDate, $endDate): Builder {
                            return $query->whereBetween('record_sortable_date', [$startDate, $endDate]);
                        }
                    )
                    ->whereHasMorph('concern', Student::class, function (Builder $query) use ($groupId) {
                        $query->when(
                            $groupId,
                            fn (Builder $query) => $this->groupFilter($query, $groupId)
                        );
                    })
            )
            ->columns([
                TextColumn::make('direction')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => Str::title($state))
                    ->icon(fn (HolisticEngagement $record) => match ($record->direction) {
                        'outbound' => 'heroicon-o-arrow-up-tray',
                        'inbound' => 'heroicon-o-arrow-down-tray',
                        default => throw new Exception('Invalid record type'),
                    })
                    ->sortable(),
                TextColumn::make('status')
                    ->state(fn (HolisticEngagement $record) => ! is_null($record->record) ? match ($record->record::class) {
                        EngagementResponse::class => $record->record->status,
                        Engagement::class => EngagementDisplayStatus::getStatus($record->record),
                        default => throw new Exception('Invalid record type'),
                    } : null)
                    ->badge(),
                TextColumn::make('sent_by')
                    ->label('Sent By')
                    ->sortable(query: function (Builder $query, string $direction) {
                        $studentModel = new Student();
                        $prospectModel = new Prospect();
                        $userModel = new User();

                        return $query->orderByRaw("
                            CASE sent_by_type
                                WHEN ? THEN (SELECT {$studentModel::displayNameKey()} FROM students WHERE {$studentModel->getKeyName()} = sent_by_id)
                                WHEN ? THEN (SELECT {$prospectModel::displayNameKey()} FROM prospects WHERE {$prospectModel->getKeyName()} = sent_by_id)
                                WHEN ? THEN (SELECT name FROM users WHERE {$userModel->getKeyName()} = sent_by_id)
                                ELSE NULL
                            END {$direction}
                        ", [$studentModel->getMorphClass(), $prospectModel->getMorphClass(), $userModel->getMorphClass()]);
                    })
                    ->state(fn (HolisticEngagement $record): ?string => $record->sentBy ? match ($record->sentBy::class) {
                        Student::class, Prospect::class => $record->sentBy->{$record->sentBy->displayNameKey()},
                        User::class => $record->sentBy->name,
                        default => throw new Exception('Invalid sender type'),
                    } : null)
                    ->url(fn (HolisticEngagement $record): ?string => $record->sentBy ? match ($record->sentBy::class) {
                        Student::class => StudentResource::getUrl('view', ['record' => $record->sentBy->getKey()]),
                        Prospect::class => ProspectResource::getUrl('view', ['record' => $record->sentBy->getKey()]),
                        default => null,
                    } : null)
                    ->openUrlInNewTab(),
                TextColumn::make('sent_to')
                    ->label('Sent To')
                    ->sortable(query: function (Builder $query, string $direction) {
                        $studentModel = new Student();
                        $prospectModel = new Prospect();

                        return $query->orderByRaw("
                            CASE sent_to_type
                                WHEN ? THEN (SELECT {$studentModel::displayNameKey()} FROM students WHERE {$studentModel->getKeyName()} = sent_to_id)
                                WHEN ? THEN (SELECT {$prospectModel::displayNameKey()} FROM prospects WHERE {$prospectModel->getKeyName()} = sent_to_id)
                                ELSE NULL
                            END {$direction}
                        ", [$studentModel->getMorphClass(), $prospectModel->getMorphClass()]);
                    })
                    ->state(fn (HolisticEngagement $record): ?string => $record->sentTo ? match ($record->sentTo::class) {
                        Student::class, Prospect::class => $record->sentTo->{$record->sentTo->displayNameKey()},
                        default => 'N/A',
                    } : 'N/A')
                    ->url(fn (HolisticEngagement $record): ?string => $record->sentTo ? match ($record->sentTo::class) {
                        Student::class => StudentResource::getUrl('view', ['record' => $record->sentTo->getKey()]),
                        Prospect::class => ProspectResource::getUrl('view', ['record' => $record->sentTo->getKey()]),
                        default => null,
                    } : null)
                    ->openUrlInNewTab(),
                TextColumn::make('type')
                    ->badge()
                    ->sortable()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'email' => 'Email',
                        'sms' => 'SMS',
                        default => throw new Exception('Invalid type'),
                    })
                    ->icon(fn (string $state) => match ($state) {
                        'email' => 'heroicon-o-envelope',
                        'sms' => 'heroicon-o-chat-bubble-bottom-center-text',
                        default => throw new Exception('Invalid type'),
                    }),
                TextColumn::make('details')
                    ->state(fn (HolisticEngagement $record) => ! is_null($record->record) ? match ($record->record::class) {
                        Engagement::class => Str::limit(match ($record->record->channel) {
                            NotificationChannel::Email => $record->record->getSubjectMarkdown(),
                            NotificationChannel::Sms => $record->record->getBodyMarkdown(),
                            default => 'N/A',
                        }, 50),
                        EngagementResponse::class => Str::limit(match ($record->record->type) {
                            EngagementResponseType::Email => $record->record->subject,
                            EngagementResponseType::Sms => $record->record->getBodyMarkdown(),
                        }, 50),
                        default => throw new Exception('Invalid record type'),
                    } : null),
                TextColumn::make('record_sortable_date')
                    ->dateTime()
                    ->label('Date')
                    ->sortable(),
                TextColumn::make('campaign')
                    ->sortable(query: function (Builder $query, string $direction) {
                        $engagementModel = new Engagement();

                        return $query->orderByRaw("
                            CASE record_type
                                WHEN ? THEN (
                                    SELECT name
                                    FROM campaigns c
                                    INNER JOIN campaign_actions ca ON ca.campaign_id = c.id
                                    WHERE ca.id = (
                                        SELECT campaign_action_id
                                        FROM engagements
                                        WHERE id = record_id
                                    )
                                )
                                ELSE NULL
                            END {$direction}
                        ", [$engagementModel->getMorphClass()]);
                    })
                    ->state(
                        fn (HolisticEngagement $record) => $record->record instanceof Engagement
                            ? $record->record->campaignAction?->campaign->name ?? 'N/A'
                            : 'N/A'
                    )
                    ->url(
                        fn (HolisticEngagement $record) => $record->record instanceof Engagement
                        ? (
                            $record->record->campaignAction?->campaign
                                ? CampaignResource::getUrl('view', ['record' => $record->record->campaignAction->campaign->getKey()])
                                : null
                        )
                        : null
                    )
                    ->openUrlInNewTab(),
            ])
            ->filters([
                SelectFilter::make('direction')
                    ->options([
                        'outbound' => 'Outbound',
                        'inbound' => 'Inbound',
                    ]),
                SelectFilter::make('type')
                    ->options([
                        'email' => 'Email',
                        'sms' => 'SMS',
                    ]),
            ])
            ->headerActions([
                ExportAction::make()
                    ->exporter(StudentMessagesDetailTableExporter::class)
                    ->formats([
                        ExportFormat::Csv,
                    ]),
            ]);
    }
}
