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

namespace AdvisingApp\StudentDataModel\Filament\Resources\StudentResource\RelationManagers;

use AdvisingApp\Engagement\Filament\ManageRelatedRecords\ManageRelatedEngagementRecords\Actions\DraftWithAiAction;
use AdvisingApp\Engagement\Filament\Resources\EngagementResource\Fields\EngagementSmsBodyField;
use AdvisingApp\Engagement\Models\Contracts\HasDeliveryMethod;
use AdvisingApp\Engagement\Models\EmailTemplate;
use AdvisingApp\Engagement\Models\Engagement;
use AdvisingApp\Engagement\Models\EngagementResponse;
use AdvisingApp\Notification\Enums\NotificationChannel;
use AdvisingApp\Notification\Enums\NotificationDeliveryStatus;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\StudentDataModel\Models\Student;
use AdvisingApp\Timeline\Models\Timeline;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Infolists\Components\Fieldset as InfolistFieldset;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use FilamentTiptapEditor\TiptapEditor;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Expression;
use Illuminate\Support\HtmlString;

class EngagementsRelationManager extends RelationManager
{
    protected static string $relationship = 'timeline';

    protected static ?string $title = 'Messages';

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema(fn (Timeline $record) => match ($record->timelineable::class) {
            Engagement::class => [
                TextEntry::make('user.name')
                    ->label('Created By')
                    ->getStateUsing(fn (Timeline $record): string => $record->timelineable->user->name),
                InfolistFieldset::make('Content')
                    ->schema([
                        TextEntry::make('subject')
                            ->getStateUsing(fn (Timeline $record): ?string => $record->timelineable->subject)
                            ->hidden(fn ($state): bool => blank($state))
                            ->columnSpanFull(),
                        TextEntry::make('body')
                            ->getStateUsing(fn (Timeline $record): HtmlString => $record->timelineable->getBody())
                            ->columnSpanFull(),
                    ]),
                InfolistFieldset::make('delivery')
                    ->label('Delivery Information')
                    ->columnSpanFull()
                    ->schema([
                        TextEntry::make('channel')
                            ->label('Channel')
                            ->getStateUsing(function (Timeline $record): string {
                                /** @var HasDeliveryMethod $timelineable */
                                $timelineable = $record->timelineable;

                                return $timelineable->getDeliveryMethod()->getLabel();
                            }),
                        IconEntry::make('latestOutboundDeliverable.delivery_status')
                            ->getStateUsing(fn (Timeline $record): ?NotificationDeliveryStatus => $record->timelineable->latestOutboundDeliverable?->delivery_status)
                            ->icon(fn (NotificationDeliveryStatus $state): string => $state->getIconClass())
                            ->color(fn (NotificationDeliveryStatus $state): string => $state->getColor())
                            ->label('Status')
                            ->default(NotificationDeliveryStatus::Awaiting),
                        TextEntry::make('latestOutboundDeliverable.delivered_at')
                            ->getStateUsing(fn (Timeline $record): string => $record->timelineable->latestOutboundDeliverable->delivered_at)
                            ->label('Delivered At')
                            ->hidden(fn (Timeline $record): bool => is_null($record->timelineable->latestOutboundDeliverable?->delivered_at)),
                        TextEntry::make('latestOutboundDeliverable.delivery_response')
                            ->getStateUsing(fn (Timeline $record): string => $record->timelineable->latestOutboundDeliverable->delivery_response)
                            ->label('Error Details')
                            ->hidden(fn (Timeline $record): bool => is_null($record->timelineable->latestOutboundDeliverable?->delivery_response)),
                    ])
                    ->columns(),
            ],
            EngagementResponse::class => [
                TextEntry::make('content'),
                TextEntry::make('sent_at')
                    ->dateTime('Y-m-d H:i:s'),
            ],
        });
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            Select::make('channel')
                ->label('What would you like to send?')
                ->options(NotificationChannel::getEngagementOptions())
                ->default(NotificationChannel::Email->value)
                ->disableOptionWhen(fn (string $value): bool => (($value == (NotificationChannel::Sms->value) && ! $this->getOwnerRecord()->canRecieveSms())) || NotificationChannel::tryFrom($value)?->getCaseDisabled())
                ->selectablePlaceholder(false)
                ->live(),
            Fieldset::make('Content')
                ->schema([
                    TextInput::make('subject')
                        ->autofocus()
                        ->required()
                        ->placeholder(__('Subject'))
                        ->hidden(fn (Get $get): bool => $get('channel') === NotificationChannel::Sms->value)
                        ->columnSpanFull(),
                    TiptapEditor::make('body')
                        ->disk('s3-public')
                        ->label('Body')
                        ->mergeTags($mergeTags = [
                            'student first name',
                            'student last name',
                            'student full name',
                            'student email',
                            'student preferred name',
                        ])
                        ->showMergeTagsInBlocksPanel(! ($form->getLivewire() instanceof RelationManager))
                        ->profile('email')
                        ->required()
                        ->hintAction(fn (TiptapEditor $component) => Action::make('loadEmailTemplate')
                            ->form([
                                Select::make('emailTemplate')
                                    ->searchable()
                                    ->options(function (Get $get): array {
                                        return EmailTemplate::query()
                                            ->when(
                                                $get('onlyMyTemplates'),
                                                fn (Builder $query) => $query->whereBelongsTo(auth()->user())
                                            )
                                            ->orderBy('name')
                                            ->limit(50)
                                            ->pluck('name', 'id')
                                            ->toArray();
                                    })
                                    ->getSearchResultsUsing(function (Get $get, string $search): array {
                                        return EmailTemplate::query()
                                            ->when(
                                                $get('onlyMyTemplates'),
                                                fn (Builder $query) => $query->whereBelongsTo(auth()->user())
                                            )
                                            ->when(
                                                $get('onlyMyTeamTemplates'),
                                                fn (Builder $query) => $query->whereIn('user_id', auth()->user()->teams->users->pluck('id'))
                                            )
                                            ->where(new Expression('lower(name)'), 'like', "%{$search}%")
                                            ->orderBy('name')
                                            ->limit(50)
                                            ->pluck('name', 'id')
                                            ->toArray();
                                    }),
                                Checkbox::make('onlyMyTemplates')
                                    ->label('Only show my templates')
                                    ->live()
                                    ->afterStateUpdated(fn (Set $set) => $set('emailTemplate', null)),
                                Checkbox::make('onlyMyTeamTemplates')
                                    ->label("Only show my team's templates")
                                    ->live()
                                    ->afterStateUpdated(fn (Set $set) => $set('emailTemplate', null)),
                            ])
                            ->action(function (array $data) use ($component) {
                                $template = EmailTemplate::find($data['emailTemplate']);

                                if (! $template) {
                                    return;
                                }

                                $component->state(
                                    $component->generateImageUrls($template->content),
                                );
                            }))
                        ->hidden(fn (Get $get): bool => $get('channel') === NotificationChannel::Sms->value)
                        ->helperText('You can insert student information by typing {{ and choosing a merge value to insert.')
                        ->columnSpanFull(),
                    EngagementSmsBodyField::make(context: 'create', form: $form),
                    Actions::make([
                        DraftWithAiAction::make()
                            ->mergeTags($mergeTags),
                    ]),
                ]),
            Fieldset::make('Send your email or text')
                ->schema([
                    Toggle::make('send_later')
                        ->reactive()
                        ->helperText('By default, this email or text will send as soon as it is created unless you schedule it to send later.'),
                    DateTimePicker::make('deliver_at')
                        ->required()
                        ->visible(fn (Get $get) => $get('send_later')),
                ]),
        ]);
    }

    public function table(Table $table): Table
    {
        $canAccessEngagements = auth()->user()->can('viewAny', Engagement::class);
        $canAccessEngagementResponses = auth()->user()->can('viewAny', EngagementResponse::class);

        return $table
            ->emptyStateHeading('No email or text messages.')
            ->emptyStateDescription('Create an email or text message to get started.')
            ->defaultSort('record_sortable_date', 'desc')
            ->modifyQueryUsing(fn (Builder $query) => $query->whereHasMorph('timelineable', [
                ...($canAccessEngagements ? [Engagement::class] : []),
                ...($canAccessEngagements ? [EngagementResponse::class] : []),
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
                    ->getStateUsing(function (Timeline $record) {
                        /** @var HasDeliveryMethod $timelineable */
                        $timelineable = $record->timelineable;

                        return $timelineable->getDeliveryMethod();
                    }),
                TextColumn::make('record_sortable_date')
                    ->label('Date')
                    ->sortable(),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('New Email or Text')
                    ->modalHeading('Create new email or text')
                    ->authorize(function () {
                        $ownerRecord = $this->getOwnerRecord();

                        return auth()->user()->can('create', [Engagement::class, $ownerRecord instanceof Prospect ? $ownerRecord : null]);
                    })
                    ->createAnother(false)
                    ->action(function (CreateAction $action, array $data, Form $form) {
                        if ($data['channel'] == NotificationChannel::Sms->value && ! $this->getOwnerRecord()->canRecieveSms()) {
                            Notification::make()
                                ->title('Student does not have mobile number.')
                                ->danger()
                                ->send();

                            $action->halt();
                        }

                        /** @var Student $record */
                        $record = $this->getOwnerRecord();

                        $engagement = new Engagement($data);
                        $engagement->recipient()->associate($record);
                        $engagement->save();

                        $form->model($engagement)->saveRelationships();
                    }),
            ])
            ->actions([
                ViewAction::make()
                    ->modalHeading(function (Timeline $record) {
                        /** @var HasDeliveryMethod $timelineable */
                        $timelineable = $record->timelineable;

                        return "View {$timelineable->getDeliveryMethod()->getLabel()}";
                    }),
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
