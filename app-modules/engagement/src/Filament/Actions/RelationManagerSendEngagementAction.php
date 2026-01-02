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

namespace AdvisingApp\Engagement\Filament\Actions;

use AdvisingApp\Engagement\Actions\CreateEngagement;
use AdvisingApp\Engagement\DataTransferObjects\EngagementCreationData;
use AdvisingApp\Engagement\Filament\Forms\Components\EngagementSmsBodyInput;
use AdvisingApp\Engagement\Models\EmailTemplate;
use AdvisingApp\Engagement\Models\Engagement;
use AdvisingApp\Notification\Enums\NotificationChannel;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\Prospect\Models\ProspectEmailAddress;
use AdvisingApp\StudentDataModel\Models\Contracts\Educatable;
use AdvisingApp\StudentDataModel\Models\Student;
use AdvisingApp\StudentDataModel\Models\StudentEmailAddress;
use AdvisingApp\StudentDataModel\Models\StudentPhoneNumber;
use Exception;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Schemas\Schema;
use FilamentTiptapEditor\Enums\TiptapOutput;
use FilamentTiptapEditor\TiptapEditor;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Expression;
use Illuminate\Support\Carbon;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class RelationManagerSendEngagementAction extends CreateAction
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->icon('heroicon-m-chat-bubble-bottom-center-text')
            ->label('New')
            ->modalHeading('Create new email or text')
            ->model(Engagement::class)
            ->authorize(function (RelationManager $livewire) {
                $ownerRecord = $livewire->getOwnerRecord();

                return auth()->user()->can('create', [Engagement::class, $ownerRecord instanceof Prospect ? $ownerRecord : null]);
            })
            ->steps(fn (): array => [
                Step::make('Contact Information')
                    ->schema([
                        Select::make('channel')
                            ->label('What would you like to send?')
                            ->options(NotificationChannel::getEngagementOptions())
                            ->default(function (RelationManager $livewire): ?string {
                                assert($livewire->getOwnerRecord() instanceof Educatable);

                                return $livewire->getOwnerRecord()->emailAddresses()->whereDoesntHave('bounced')->exists()
                                    ? NotificationChannel::Email->value
                                    : ($livewire->getOwnerRecord()->phoneNumbers()
                                        ->where('can_receive_sms', true)
                                        ->whereDoesntHave('smsOptOut')
                                        ->exists() ? NotificationChannel::Sms->value : null);
                            })
                            ->disableOptionWhen(
                                function (RelationManager $livewire, string $value): bool {
                                    if (NotificationChannel::tryFrom($value)?->getCaseDisabled() ?? false) {
                                        return true;
                                    }

                                    assert($livewire->getOwnerRecord() instanceof Educatable);

                                    if (($value == NotificationChannel::Email->value)) {
                                        return ! $livewire->getOwnerRecord()
                                            ->emailAddresses()
                                            ->whereDoesntHave('bounced')
                                            ->exists();
                                    }

                                    if (($value == NotificationChannel::Sms->value)) {
                                        return ! $livewire->getOwnerRecord()
                                            ->phoneNumbers()
                                            ->where('can_receive_sms', true)
                                            ->whereDoesntHave('smsOptOut')
                                            ->exists();
                                    }

                                    return true;
                                }
                            )
                            ->selectablePlaceholder(false)
                            ->live()
                            ->afterStateUpdated(function (mixed $state, RelationManager $livewire, Set $set) {
                                assert($livewire->getOwnerRecord() instanceof Educatable);

                                $channel = NotificationChannel::parse($state);
                                $educatable = $livewire->getOwnerRecord();

                                $route = match ($channel) {
                                    NotificationChannel::Email => $educatable->primaryEmailAddress()
                                        ->whereDoesntHave('bounced')
                                        ->first()?->getKey(),
                                    NotificationChannel::Sms => $educatable->primaryPhoneNumber()
                                        ->where('can_receive_sms', true)
                                        ->whereDoesntHave('smsOptOut')
                                        ->first()?->getKey(),
                                    default => null,
                                } ?? match ($channel) {
                                    NotificationChannel::Email => $educatable->emailAddresses()
                                        ->whereDoesntHave('bounced')
                                        ->first()?->getKey(),
                                    NotificationChannel::Sms => $educatable->phoneNumbers()
                                        ->where('can_receive_sms', true)
                                        ->whereDoesntHave('smsOptOut')
                                        ->first()?->getKey(),
                                    default => null,
                                };

                                $set('recipient_route_id', $route);
                            }),
                        Select::make('recipient_route_id')
                            ->label(fn (Get $get): string => match (NotificationChannel::parse($get('channel'))) {
                                NotificationChannel::Email => 'Email address',
                                NotificationChannel::Sms => 'Phone number',
                            })
                            ->options(function (Get $get, RelationManager $livewire): array {
                                assert($livewire->getOwnerRecord() instanceof Student || $livewire->getOwnerRecord() instanceof Prospect);

                                return match (NotificationChannel::parse($get('channel'))) {
                                    NotificationChannel::Email => $livewire->getOwnerRecord()->emailAddresses()
                                        ->whereDoesntHave('bounced')
                                        ->get()
                                        ->mapWithKeys(fn (StudentEmailAddress | ProspectEmailAddress $emailAddress): array => [
                                            $emailAddress->getKey() => $emailAddress->address . (filled($emailAddress->type) ? " ({$emailAddress->type})" : ''),
                                        ])
                                        ->all(),
                                    NotificationChannel::Sms => $livewire->getOwnerRecord()->phoneNumbers()
                                        ->where('can_receive_sms', true)
                                        ->whereDoesntHave('smsOptOut')
                                        ->get()
                                        ->mapWithKeys(fn (StudentPhoneNumber $phoneNumber): array => [
                                            $phoneNumber->getKey() => $phoneNumber->number . (filled($phoneNumber->ext) ? " (ext. {$phoneNumber->ext})" : '') . (filled($phoneNumber->type) ? " ({$phoneNumber->type})" : ''),
                                        ])
                                        ->all(),
                                };
                            })
                            ->default(function (RelationManager $livewire): ?string {
                                assert($livewire->getOwnerRecord() instanceof Educatable);

                                return $livewire->getOwnerRecord()->emailAddresses()
                                    ->whereDoesntHave('bounced')
                                    ->orderBy('order')
                                    ->first()
                                    ?->getKey()
                                    ?? $livewire->getOwnerRecord()->phoneNumbers()
                                        ->where('can_receive_sms', true)
                                        ->whereDoesntHave('smsOptOut')
                                        ->orderBy('order')
                                        ->first()
                                        ?->getKey();
                            })
                            ->required(),
                    ])
                    ->columns(2),
                Step::make('Content')
                    ->schema([
                        TiptapEditor::make('subject')
                            ->label('Subject')
                            ->mergeTags([
                                'recipient first name',
                                'recipient last name',
                                'recipient full name',
                                'recipient email',
                                'recipient preferred name',
                                'user first name',
                                'user full name',
                                'user job title',
                                'user email',
                                'user phone number',
                            ])
                            ->showMergeTagsInBlocksPanel(false)
                            ->helperText('You may use “merge tags” to substitute information about a recipient into your subject line. Insert a “{{“ in the subject line field to see a list of available merge tags')
                            ->hidden(fn (Get $get): bool => $get('channel') === NotificationChannel::Sms->value)
                            ->profile('sms')
                            ->required()
                            ->placeholder('Enter the email subject here...')
                            ->columnSpanFull(),
                        TiptapEditor::make('body')
                            ->disk('s3-public')
                            ->label('Body')
                            ->mergeTags($mergeTags = [
                                'recipient first name',
                                'recipient last name',
                                'recipient full name',
                                'recipient email',
                                'recipient preferred name',
                                'user first name',
                                'user full name',
                                'user job title',
                                'user email',
                                'user phone number',
                            ])
                            ->profile('email')
                            ->required()
                            ->hintAction(fn (TiptapEditor $component) => Action::make('loadEmailTemplate')
                                ->schema([
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
                                                    fn (Builder $query) => $query->whereIn('user_id', auth()->user()->team->users->pluck('id'))
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
                            ->helperText('You can insert recipient or your information by typing {{ and choosing a merge value to insert.')
                            ->columnSpanFull(),
                        EngagementSmsBodyInput::make(context: 'create'),
                        Actions::make([
                            RelationManagerDraftWithAiAction::make()
                                ->mergeTags($mergeTags),
                        ]),
                    ]),
                Step::make('Email Signature')
                    ->schema([
                        Toggle::make('is_signature_enabled')
                            ->label('Include signature')
                            ->helperText('You may configure your email signature in Profile Settings by selecting your avatar in the upper right portion of the screen.')
                            ->live(),
                        TiptapEditor::make('signature')
                            ->profile('signature')
                            ->extraInputAttributes(['style' => 'min-height: 12rem;'])
                            ->output(TiptapOutput::Json)
                            ->required(fn (Get $get) => $get('is_signature_enabled'))
                            ->disk('s3-public')
                            ->visible(fn (Get $get) => $get('is_signature_enabled'))
                            ->default(auth()->user()->signature)
                            // By default, the TipTap editor will attempt to save relationships to media items, but these will instead be saved as part of the main body content.
                            ->saveRelationshipsUsing(null),
                    ])
                    ->visible(auth()->user()->is_signature_enabled)
                    ->hidden(fn (Get $get): bool => $get('channel') === NotificationChannel::Sms->value),
                Step::make('Send Your Message')
                    ->schema([
                        Toggle::make('send_later')
                            ->reactive()
                            ->helperText('By default, this message will send as soon as it is created unless you schedule it to send later.'),
                        DateTimePicker::make('scheduled_at')
                            ->required()
                            ->visible(fn (Get $get) => $get('send_later')),
                    ]),
            ])
            ->action(function (array $data, Schema $schema, RelationManager $livewire) {
                $recipient = $livewire->getOwnerRecord();

                throw_if(
                    ! $recipient instanceof Educatable,
                    new Exception('Recipient must implement Educatable interface.'),
                );

                $data['subject'] ??= ['type' => 'doc', 'content' => []];
                $data['subject']['content'] = [
                    ...($data['subject']['content'] ?? []),
                ];

                $data['body'] ??= ['type' => 'doc', 'content' => []];
                $data['body']['content'] = [
                    ...($data['body']['content'] ?? []),
                    ...($data['signature']['content'] ?? []),
                ];

                $formFields = $schema->getFlatFields();

                $channel = NotificationChannel::parse($data['channel']);

                $recipientRoute = match ($channel) {
                    NotificationChannel::Email => $recipient->emailAddresses()->find($data['recipient_route_id'] ?? null)?->address,
                    NotificationChannel::Sms => $recipient->phoneNumbers()->find($data['recipient_route_id'] ?? null)?->number,
                    default => null,
                };

                $engagement = app(CreateEngagement::class)->execute(new EngagementCreationData(
                    user: auth()->user(),
                    recipient: $recipient,
                    channel: $channel,
                    subject: $data['subject'] ?? null,
                    body: $data['body'] ?? null,
                    temporaryBodyImages: [
                        ...array_map(
                            fn (TemporaryUploadedFile $file): array => [
                                'extension' => $file->getClientOriginalExtension(),
                                'path' => (fn () => $this->path)->call($file),
                            ],
                            $formFields['body']->getTemporaryImages(),
                        ),
                        ...(($formFields['signature'] ?? null) ? array_map(
                            fn (TemporaryUploadedFile $file): array => [
                                'extension' => $file->getClientOriginalExtension(),
                                'path' => (fn () => $this->path)->call($file),
                            ],
                            $formFields['signature']->getTemporaryImages(),
                        ) : []),
                    ],
                    scheduledAt: ($data['send_later'] ?? false) ? Carbon::parse($data['scheduled_at'] ?? null) : null,
                    recipientRoute: $recipientRoute,
                ));

                $schema->model($engagement)->saveRelationships();
            })
            ->modalSubmitActionLabel('Send')
            ->modalCloseButton(false)
            ->closeModalByClickingAway(false)
            ->closeModalByEscaping(false)
            ->createAnother(false);
    }

    public static function getDefaultName(): ?string
    {
        return 'engage';
    }
}
