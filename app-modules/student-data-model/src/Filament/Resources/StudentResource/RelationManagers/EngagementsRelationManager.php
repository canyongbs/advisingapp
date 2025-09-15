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

namespace AdvisingApp\StudentDataModel\Filament\Resources\StudentResource\RelationManagers;

use AdvisingApp\Engagement\Enums\EngagementDisplayStatus;
use AdvisingApp\Engagement\Enums\EngagementResponseStatus;
use AdvisingApp\Engagement\Filament\Actions\RelationManagerSendEngagementAction;
use AdvisingApp\Engagement\Models\Contracts\HasDeliveryMethod;
use AdvisingApp\Engagement\Models\Engagement;
use AdvisingApp\Engagement\Models\EngagementResponse;
use AdvisingApp\Notification\Enums\NotificationChannel;
use AdvisingApp\Notification\Models\EmailMessageEvent;
use AdvisingApp\Notification\Models\SmsMessageEvent;
use AdvisingApp\Timeline\Models\Timeline;
use App\Infolists\Components\EngagementBody;
use Filament\Infolists\Components\Fieldset as InfolistFieldset;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Split;
use Filament\Infolists\Components\Tabs;
use Filament\Infolists\Components\Tabs\Tab;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use Livewire\Attributes\On;

class EngagementsRelationManager extends RelationManager
{
    protected static string $relationship = 'timeline';

    protected static ?string $title = 'Messages';

    #[On('engagement-sent')]
    public function refresh(): void {}

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema(fn (Timeline $record) => match ($record->timelineable::class) {
            Engagement::class => [
                Tabs::make()
                    ->columnSpanFull()
                    ->tabs([
                        Tab::make('Content')
                            ->schema([
                                TextEntry::make('user.name')
                                    ->label('Created By')
                                    ->getStateUsing(fn (Timeline $record): string => $record->timelineable->user->name ?? 'N/A'),
                                InfolistFieldset::make('Content')
                                    ->schema([
                                        TextEntry::make('subject')
                                            ->getStateUsing(function (Timeline $record): ?string {
                                                $model = $record->timelineable;

                                                if ($model instanceof Engagement && $model->channel === NotificationChannel::Email) {
                                                    return (string) $model->getSubject();
                                                }

                                                return null;
                                            })
                                            ->visible(fn (Timeline $record): bool => $record->timelineable instanceof Engagement && $record->timelineable->channel === NotificationChannel::Email)
                                            ->columnSpanFull(),
                                        EngagementBody::make('body')
                                            ->getStateUsing(fn (Timeline $record): HtmlString => $record->timelineable->getBody())
                                            ->columnSpanFull(),
                                    ]),
                            ]),
                        Tab::make('Events')
                            ->schema([
                                RepeatableEntry::make('message_events')
                                    ->state(function (Timeline $record) {
                                        $timelineable = $record->timelineable;

                                        if ($timelineable instanceof Engagement) {
                                            return match ($timelineable->channel) {
                                                NotificationChannel::Email => $timelineable->latestEmailMessage?->events()?->orderBy('occurred_at', 'desc')->get() ?? [],
                                                NotificationChannel::Sms => $timelineable->latestSmsMessage?->events()?->orderBy('occurred_at', 'desc')->get() ?? [],
                                                default => [],
                                            };
                                        }
                                    })
                                    ->schema([
                                        Section::make()
                                            ->schema([
                                                TextEntry::make('type')
                                                    ->getStateUsing(fn (EmailMessageEvent|SmsMessageEvent $record): string => $record->type?->getLabel()),
                                                TextEntry::make('occured_at')
                                                    ->dateTime()
                                                    ->getStateUsing(fn (EmailMessageEvent|SmsMessageEvent $record): string => $record->occurred_at->format('Y-m-d H:i:s')),
                                            ])
                                            ->columns(),
                                    ])
                                    ->contained(false),
                            ]),
                    ]),
            ],
            EngagementResponse::class => [
                Split::make([
                    Section::make([
                        TextEntry::make('subject')
                            ->getStateUsing(fn (Timeline $record): ?string => $record->timelineable->subject)
                            ->hidden(fn ($state): bool => blank($state))
                            ->columnSpanFull(),
                        EngagementBody::make('body')
                            ->getStateUsing(fn (Timeline $record): HtmlString => $record->timelineable->getBody())
                            ->columnSpanFull(),
                    ]),
                    Section::make([
                        TextEntry::make('sent_at')
                            ->dateTime()
                            ->getStateUsing(fn (Timeline $record): string => $record->timelineable->sent_at),
                    ])->grow(false),
                ])
                    ->from('md')
                    ->columnSpanFull(),
            ],
        });
    }

    public function table(Table $table): Table
    {
        $canAccessEngagements = auth()->user()->can('viewAny', Engagement::class);
        $canAccessEngagementResponses = auth()->user()->can('viewAny', EngagementResponse::class);

        return $table
            ->emptyStateHeading('No email or text messages.')
            ->emptyStateDescription('Create an email or text message to get started.')
            ->defaultSort('record_sortable_date', 'desc')
            ->modifyQueryUsing(
                fn (Builder $query) => $query
                    ->whereHasMorph('timelineable', [
                        ...($canAccessEngagements ? [Engagement::class] : []),
                        ...($canAccessEngagementResponses ? [EngagementResponse::class] : []),
                    ])
                    ->with([
                        'timelineable' => function ($morphQuery) use ($canAccessEngagements) {
                            $morphQuery->when(
                                $canAccessEngagements && $morphQuery->getModel() instanceof Engagement,
                                fn (Builder $query) => $query->with(['latestEmailMessage.events', 'latestSmsMessage.events'])
                            );
                        },
                    ])
            )
            ->columns([
                TextColumn::make('direction')
                    ->getStateUsing(fn (Timeline $record) => match ($record->timelineable::class) {
                        Engagement::class => 'Outbound',
                        EngagementResponse::class => 'Inbound',
                        default => '',
                    })
                    ->icon(fn (string $state) => match ($state) {
                        'Outbound' => 'heroicon-o-arrow-up-tray',
                        'Inbound' => 'heroicon-o-arrow-down-tray',
                    }),
                TextColumn::make('status')
                    ->getStateUsing(fn (Timeline $record) => match ($record->timelineable::class) {
                        EngagementResponse::class => $record->timelineable->status,
                        Engagement::class => EngagementDisplayStatus::getStatus($record->timelineable),
                    })
                    ->badge(),
                TextColumn::make('subject')
                    ->label('Preview')
                    ->description(
                        fn (Timeline $record): ?string => ($record->timelineable instanceof Engagement || $record->timelineable instanceof EngagementResponse) && filled($body = $record->timelineable->getBodyMarkdown())
                            ? Str::limit(strip_tags($body), 50)
                            : null
                    )
                    ->getStateUsing(function (Timeline $record): ?string {
                        return $record->timelineable instanceof EngagementResponse || $record->timelineable instanceof Engagement
                            ? $record->timelineable->getSubject()
                            : null;
                    }),
                TextColumn::make('type')
                    ->getStateUsing(function (Timeline $record) {
                        /** @var HasDeliveryMethod $timelineable */
                        $timelineable = $record->timelineable;

                        return $timelineable->getDeliveryMethod();
                    }),
                TextColumn::make('record_sortable_date')
                    ->label('Date')
                    ->dateTime()
                    ->sortable(),
            ])
            ->headerActions([
                RelationManagerSendEngagementAction::make(),
            ])
            ->actions([
                ViewAction::make()
                    ->modalHeading(function (Timeline $record): Htmlable {
                        $status = match ($record->timelineable::class) {
                            EngagementResponse::class => $record->timelineable->status->getLabel(),
                            default => ''
                        };

                        return new HtmlString(view('student-data-model::components.filament.resources.educatable-resource.view-educatable.engagement-modal-header', ['record' => $record, 'status' => $status]));
                    })
                    ->extraModalFooterActions([
                        Action::make('updateStatus')
                            ->label(
                                function (Timeline $record) {
                                    /** @var EngagementResponse $timelineable */
                                    $timelineable = $record->timelineable;

                                    return $timelineable->status === EngagementResponseStatus::New ? 'Mark as Actioned' : 'Mark as New';
                                }
                            )
                            ->color('gray')
                            ->action(
                                function (Timeline $record) {
                                    /** @var EngagementResponse $timelineable */
                                    $timelineable = $record->timelineable;

                                    if ($timelineable->status === EngagementResponseStatus::New) {
                                        $timelineable->update(['status' => EngagementResponseStatus::Actioned]);
                                    } else {
                                        $timelineable->update(['status' => EngagementResponseStatus::New]);
                                    }

                                    Notification::make()
                                        ->success()
                                        ->title('Status updated!')
                                        ->send();
                                }
                            )
                            ->visible(fn (Timeline $record): bool => $record->timelineable instanceof EngagementResponse),
                    ]),
            ])
            ->filters([
                SelectFilter::make('direction')
                    ->options([
                        Engagement::class => 'Outbound',
                        EngagementResponse::class => 'Inbound',
                    ])
                    ->modifyQueryUsing(
                        fn (Builder $query, array $data) => $query
                            ->when($data['value'], fn (Builder $query) => $query->whereHasMorph('timelineable', $data['value']))
                    )
                    ->visible($canAccessEngagements && $canAccessEngagementResponses),
                SelectFilter::make('type')
                    ->options(NotificationChannel::class)
                    ->modifyQueryUsing(
                        fn (Builder $query, array $data) => $query
                            ->when(
                                $data['value'] === NotificationChannel::Email->value,
                                fn (Builder $query) => $query
                                    ->whereHasMorph(
                                        'timelineable',
                                        [Engagement::class],
                                        fn (Builder $query, string $type) => match ($type) {
                                            Engagement::class => $query->where('channel', $data['value']),
                                        }
                                    )
                            )
                            ->when(
                                $data['value'] === NotificationChannel::Sms->value,
                                fn (Builder $query) => $query->whereHasMorph(
                                    'timelineable',
                                    [Engagement::class, EngagementResponse::class],
                                    fn (Builder $query, string $type) => match ($type) {
                                        Engagement::class => $query->where('channel', $data['value']),
                                        EngagementResponse::class => $query,
                                    }
                                )
                            )
                    ),
            ]);
    }

    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        return auth()->user()->can('viewAny', Engagement::class)
            || auth()->user()->can('viewAny', EngagementResponse::class);
    }
}
