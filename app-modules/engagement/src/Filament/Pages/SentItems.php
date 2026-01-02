<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS LLC. All rights reserved.

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

namespace AdvisingApp\Engagement\Filament\Pages;

use AdvisingApp\Authorization\Enums\LicenseType;
use AdvisingApp\Engagement\Enums\EngagementDisplayStatus;
use AdvisingApp\Engagement\Filament\Actions\SendEngagementAction;
use AdvisingApp\Engagement\Models\Engagement;
use AdvisingApp\Group\Actions\TranslateGroupFilters;
use AdvisingApp\Group\Enums\GroupModel;
use AdvisingApp\Group\Models\Group;
use AdvisingApp\Notification\Enums\EmailMessageEventType;
use AdvisingApp\Notification\Enums\NotificationChannel;
use AdvisingApp\Notification\Enums\SmsMessageEventType;
use AdvisingApp\Prospect\Filament\Resources\Prospects\ProspectResource;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\StudentDataModel\Filament\Resources\Students\StudentResource;
use AdvisingApp\StudentDataModel\Models\Student;
use App\Filament\Clusters\UnifiedInbox;
use App\Models\User;
use Filament\Actions\ViewAction;
use Filament\Navigation\NavigationItem;
use Filament\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class SentItems extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationLabel = 'Sent Items';

    protected string $view = 'engagement::filament.pages.sent-items';

    protected static ?string $cluster = UnifiedInbox::class;

    public static function canAccess(): bool
    {
        $user = auth()->user();

        assert($user instanceof User);

        if (! $user->can('viewAny', Engagement::class)) {
            return false;
        }

        if (! $user->hasAnyLicense([LicenseType::RetentionCrm, LicenseType::RecruitmentCrm])) {
            return false;
        }

        // This authorization check has been preserved from the original message center.
        return $user->can('engagement.*.view');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Engagement::query()->whereHas('recipient')
            )
            ->columns([
                TextColumn::make('direction')
                    ->state('Outbound')
                    ->icon('heroicon-o-arrow-up-tray')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->badge(),
                TextColumn::make('status')
                    ->badge()
                    ->state(fn (Engagement $record) => EngagementDisplayStatus::getStatus($record)),
                TextColumn::make('user.name')
                    ->label('From'),
                TextColumn::make('recipient.full_name')
                    ->label('To')
                    ->url(fn (Engagement $record): ?string => match (true) {
                        $record->recipient instanceof Student => StudentResource::getUrl('view', ['record' => $record->recipient]),
                        $record->recipient instanceof Prospect => ProspectResource::getUrl('view', ['record' => $record->recipient]),
                        default => null,
                    })
                    ->openUrlInNewTab(),
                TextColumn::make('channel')
                    ->label('Type')
                    ->state(fn (Engagement $record): string => $record->channel->getLabel())
                    ->icon(fn (Engagement $record): string => $record->channel->getIcon()),
                TextColumn::make('subject')
                    ->description(
                        fn (Engagement $record): ?string => filled($body = $record->getBodyMarkdown())
                            ? Str::limit(strip_tags($body), 50)
                            : null
                    )
                    ->state(fn (Engagement $record): string => strip_tags($record->getSubjectMarkdown()))
                    ->searchable(['subject', 'body']),
                TextColumn::make('dispatched_at')
                    ->label('Date')
                    ->dateTime()
                    ->sortable(),
            ])
            ->recordActions([
                ViewAction::make()
                    ->url(fn (Engagement $record): string => ViewEngagement::getUrl(['record' => $record])),
            ])
            ->recordUrl(fn (Engagement $record): string => ViewEngagement::getUrl(['record' => $record]))
            ->defaultSort('dispatched_at', 'desc')
            ->emptyStateHeading('No Engagements yet.')
            ->filters([
                Filter::make('subscribed')
                    ->query(fn (Builder $query): Builder => $query->whereRelation('recipient.subscriptions.user', 'id', auth()->id())),
                Filter::make('care_team')
                    ->label('Care Team')
                    ->query(
                        function (Builder $query) {
                            return $query
                                ->whereRelation('recipient.careTeam', 'user_id', '=', auth()->id())
                                ->get();
                        }
                    ),
                SelectFilter::make('my_groups')
                    ->label('My Population Groups')
                    ->options(
                        auth()->user()->groups()
                            ->pluck('name', 'id'),
                    )
                    ->searchable()
                    ->optionsLimit(20)
                    ->query(fn (Builder $query, array $data) => $this->groupFilter($query, $data)),
                SelectFilter::make('all_groups')
                    ->label('All Population Groups')
                    ->options(
                        Group::all()
                            ->pluck('name', 'id'),
                    )
                    ->searchable()
                    ->optionsLimit(20)
                    ->query(fn (Builder $query, array $data) => $this->groupFilter($query, $data)),
                SelectFilter::make('status')
                    ->multiple()
                    ->label('Status')
                    ->options(collect(EngagementDisplayStatus::cases())->mapWithKeys(fn ($status) => [$status->name => $status->getLabel()]))
                    ->query(function (Builder $query, array $data): Builder {
                        if (empty($data['values'])) {
                            return $query;
                        }

                        return $query->where(function (Builder $query) use ($data) {
                            foreach ($data['values'] as $status) {
                                $status = constant(EngagementDisplayStatus::class . '::' . $status);
                                $query->orWhere(function (Builder $subQuery) use ($status) {
                                    if ($status === EngagementDisplayStatus::Scheduled) {
                                        $subQuery->whereNotNull('scheduled_at');

                                        return;
                                    }

                                    // For Email channel
                                    $subQuery->where(function (Builder $channelQuery) use ($status) {
                                        $channelQuery->where('channel', NotificationChannel::Email)
                                            ->whereHas('latestEmailMessage.events', function (Builder $query) use ($status) {
                                                match ($status) {
                                                    EngagementDisplayStatus::Pending => $query->where('type', EmailMessageEventType::Dispatched),
                                                    EngagementDisplayStatus::Sent => $query->where('type', EmailMessageEventType::Send),
                                                    EngagementDisplayStatus::Delivered => $query->where('type', EmailMessageEventType::Delivery),
                                                    EngagementDisplayStatus::Read => $query->where('type', EmailMessageEventType::Open),
                                                    EngagementDisplayStatus::Failed => $query->whereIn('type', [
                                                        EmailMessageEventType::FailedDispatch,
                                                        EmailMessageEventType::RateLimited,
                                                        EmailMessageEventType::Reject,
                                                    ]),
                                                    EngagementDisplayStatus::Bounced => $query->where('type', EmailMessageEventType::Bounce),
                                                    EngagementDisplayStatus::Complaint => $query->where('type', EmailMessageEventType::Complaint),
                                                    default => $query
                                                };
                                            });

                                        // For SMS channel
                                        $channelQuery->orWhere('channel', NotificationChannel::Sms)
                                            ->whereHas('latestSmsMessage.events', function (Builder $query) use ($status) {
                                                match ($status) {
                                                    EngagementDisplayStatus::Pending => $query->where('type', SmsMessageEventType::Dispatched),
                                                    EngagementDisplayStatus::Sent => $query->where('type', SmsMessageEventType::Sent),
                                                    EngagementDisplayStatus::Delivered => $query->where('type', SmsMessageEventType::Delivered),
                                                    EngagementDisplayStatus::Read => $query->where('type', SmsMessageEventType::Read),
                                                    EngagementDisplayStatus::Failed => $query->where('type', SmsMessageEventType::Undelivered),
                                                    default => $query
                                                };
                                            });
                                    });
                                });
                            }

                            return $query;
                        });
                    })
                    ->searchable()
                    ->preload(),
            ]);
    }

    /**
     * @return array<NavigationItem>
     */
    public static function getNavigationItems(): array
    {
        return [
            parent::getNavigationItems()[0]
                ->isActiveWhen(fn (): bool => request()->routeIs(static::getNavigationItemActiveRoutePattern(), ViewEngagement::getNavigationItemActiveRoutePattern())),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            SendEngagementAction::make()
                ->label('New')
                ->icon(null),
        ];
    }

    /**
     * @param Builder<Engagement> $query
     * @param array<string, mixed> $data
     */
    protected function groupFilter(Builder $query, array $data): void
    {
        if (blank($data['value'])) {
            return;
        }

        $modelType = Group::find($data['value'])?->model;

        $query->whereHasMorph(
            'recipient',
            [
                Student::class,
                Prospect::class,
            ],
            function (Builder $query, string $type) use ($data, $modelType): void {
                $shouldApplyFilter = match ($type) {
                    Student::class => $modelType === GroupModel::Student,
                    Prospect::class => $modelType === GroupModel::Prospect,
                    default => false,
                };

                if ($shouldApplyFilter) {
                    app(TranslateGroupFilters::class)
                        ->applyFilterToQuery($data['value'], $query);
                } else {
                    $query->whereRaw('1 = 0');
                }
            }
        );
    }
}
