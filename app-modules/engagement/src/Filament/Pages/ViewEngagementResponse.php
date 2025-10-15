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

namespace AdvisingApp\Engagement\Filament\Pages;

use AdvisingApp\Authorization\Enums\LicenseType;
use AdvisingApp\Engagement\Actions\CreateEngagement;
use AdvisingApp\Engagement\DataTransferObjects\EngagementCreationData;
use AdvisingApp\Engagement\Enums\EngagementResponseStatus;
use AdvisingApp\Engagement\Enums\EngagementResponseType;
use AdvisingApp\Engagement\Filament\Actions\DraftWithAiAction;
use AdvisingApp\Engagement\Filament\Actions\SendEngagementAction;
use AdvisingApp\Engagement\Filament\Forms\Components\EngagementSmsBodyInput;
use AdvisingApp\Engagement\Models\Engagement;
use AdvisingApp\Engagement\Models\EngagementResponse;
use AdvisingApp\Notification\Enums\NotificationChannel;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\Prospect\Models\ProspectEmailAddress;
use AdvisingApp\Prospect\Models\ProspectPhoneNumber;
use AdvisingApp\StudentDataModel\Models\Student;
use AdvisingApp\StudentDataModel\Models\StudentEmailAddress;
use AdvisingApp\StudentDataModel\Models\StudentPhoneNumber;
use App\Filament\Clusters\UnifiedInbox;
use App\Models\User;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Panel;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Flex;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use FilamentTiptapEditor\TiptapEditor;
use Illuminate\Support\Carbon;
use Illuminate\Support\HtmlString;
use Livewire\Attributes\Locked;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

/**
 * @property-read Schema $replyForm
 */
class ViewEngagementResponse extends Page
{
    protected string $view = 'engagement::filament.pages.view-engagement-response';

    protected static ?string $cluster = UnifiedInbox::class;

    protected static bool $shouldRegisterNavigation = false;

    #[Locked]
    public EngagementResponse $record;

    /**
     * @var array<string, mixed>
     */
    public ?array $replyData = [];

    public function mount(): void
    {
        if (($this->record->sender instanceof Prospect || $this->record->sender instanceof Student) && auth()->user()->can('create', [Engagement::class, $this->record->sender instanceof Prospect ? $this->record->sender : null])) {
            $this->replyForm->fill([
                'recipient_route_id' => match ($this->record->type) {
                    EngagementResponseType::Email => $this->record->sender->primaryEmailAddress?->getKey(),
                    EngagementResponseType::Sms => $this->record->sender->phoneNumbers()->where('can_receive_sms', true)->first()?->getKey(),
                },
                'subject' => ($this->record->type === EngagementResponseType::Email) ? "RE: {$this->record->subject}" : '',
                'body' => ($this->record->type === EngagementResponseType::Email) ? $this->generateEmailReplyBody() : '',
            ]);
        }
    }

    public static function canAccess(): bool
    {
        $user = auth()->user();

        assert($user instanceof User);

        if (! $user->can('viewAny', EngagementResponse::class)) {
            return false;
        }

        if (! $user->hasAnyLicense([LicenseType::RetentionCrm, LicenseType::RecruitmentCrm])) {
            return false;
        }

        // This authorization check has been preserved from the original message center.
        return $user->can('engagement_response.*.view');
    }

    /**
     * @return array<string>
     */
    public function getBreadcrumbs(): array
    {
        return static::getCluster()::unshiftClusterBreadcrumbs([
            Inbox::getUrl() => 'Inbox',
        ]);
    }

    public static function getRoutePath(Panel $panel): string
    {
        return 'inbox/{record}';
    }

    public function getTitle(): string
    {
        return $this->record->subject ?: 'Inbox';
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Flex::make([
                    Section::make([
                        TextEntry::make('subject')
                            ->columnSpanFull(),
                        TextEntry::make('content')
                            ->state(fn (EngagementResponse $record): HtmlString => $record->getBody())
                            ->columnSpanFull(),
                    ]),
                    Section::make([
                        TextEntry::make('sent_at')
                            ->dateTime(),
                    ])->grow(false),
                ])
                    ->from('md')
                    ->columnSpanFull(),
            ])
            ->record($this->record);
    }

    public function replyForm(Schema $schema): Schema
    {
        assert($this->record->sender instanceof Student || $this->record->sender instanceof Prospect);

        return $schema
            ->components([
                Select::make('recipient_route_id')
                    ->label(fn (): string => match ($this->record->type) {
                        EngagementResponseType::Email => 'Email address',
                        EngagementResponseType::Sms => 'Phone number',
                    })
                    ->options(fn (Get $get): array => match ($this->record->type) {
                        EngagementResponseType::Email => $this->record->sender->emailAddresses
                            ->mapWithKeys(fn (StudentEmailAddress | ProspectEmailAddress $emailAddress): array => [
                                $emailAddress->getKey() => $emailAddress->address . (filled($emailAddress->type) ? " ({$emailAddress->type})" : ''),
                            ])
                            ->all(),
                        EngagementResponseType::Sms => $this->record->sender->phoneNumbers()
                            ->where('can_receive_sms', true)
                            ->get()
                            ->mapWithKeys(fn (StudentPhoneNumber | ProspectPhoneNumber $phoneNumber): array => [
                                $phoneNumber->getKey() => $phoneNumber->number . (filled($phoneNumber->ext) ? " (ext. {$phoneNumber->ext})" : '') . (filled($phoneNumber->type) ? " ({$phoneNumber->type})" : ''),
                            ])
                            ->all(),
                    })
                    ->required(),
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
                    ->hidden($this->record->type === EngagementResponseType::Sms)
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
                    ->hidden($this->record->type === EngagementResponseType::Sms)
                    ->helperText('You can insert recipient or your information by typing {{ and choosing a merge value to insert.')
                    ->columnSpanFull(),
                EngagementSmsBodyInput::make(context: 'create', form: $schema, withTemplateAction: false)
                    ->hidden($this->record->type === EngagementResponseType::Email),
                Actions::make([
                    DraftWithAiAction::make()
                        ->mergeTags($mergeTags)
                        ->educatable($this->record->sender)
                        ->suffixContent(($this->record->type === EngagementResponseType::Email) ? $this->generateEmailReplyBody('') : null)
                        ->channel(fn (): NotificationChannel => match ($this->record->type) {
                            EngagementResponseType::Email => NotificationChannel::Email,
                            EngagementResponseType::Sms => NotificationChannel::Sms,
                        })
                        ->subject(false),
                ]),
                Fieldset::make('Send your message')
                    ->schema([
                        Toggle::make('send_later')
                            ->reactive()
                            ->helperText('By default, this message will send as soon as it is created unless you schedule it to send later.'),
                        DateTimePicker::make('scheduled_at')
                            ->required()
                            ->visible(fn (Get $get) => $get('send_later')),
                    ]),
            ])
            ->statePath('replyData')
            ->model(Engagement::class);
    }

    public function reply(): void
    {
        assert($this->record->sender instanceof Student || $this->record->sender instanceof Prospect);

        if (! auth()->user()->can('create', [Engagement::class, $this->record->sender instanceof Prospect ? $this->record->sender : null])) {
            abort(403);
        }

        $data = $this->replyForm->getState();

        /** @var Student | Prospect $recipient */
        $recipient = $this->record->sender;
        $data['subject'] ??= ['type' => 'doc', 'content' => []];
        $data['subject']['content'] = [
            ...($data['subject']['content'] ?? []),
        ];
        $data['body'] ??= ['type' => 'doc', 'content' => []];

        $formFields = $this->replyForm->getFlatFields();

        /** @var TiptapEditor $bodyField */
        $bodyField = $formFields['body'] ?? null;

        $channel = NotificationChannel::parse($this->record->type->value);

        $recipientRoute = match ($channel) {
            NotificationChannel::Email => $recipient->emailAddresses()->find($data['recipient_route_id'] ?? null)?->address,
            NotificationChannel::Sms => $recipient->phoneNumbers()->find($data['recipient_route_id'] ?? null)?->number,
            default => null,
        };

        $engagement = app(CreateEngagement::class)->execute(new EngagementCreationData(
            user: auth()->user(),
            recipient: $recipient,
            channel: $channel,
            subject: $data['subject'],
            body: $data['body'],
            temporaryBodyImages: [
                ...array_map(
                    fn (TemporaryUploadedFile $file): array => [
                        'extension' => $file->getClientOriginalExtension(),
                        'path' => (fn () => $this->path)->call($file),
                    ],
                    $bodyField->getTemporaryImages(),
                ),
            ],
            scheduledAt: ($data['send_later'] ?? false) ? Carbon::parse($data['scheduled_at'] ?? null) : null,
            recipientRoute: $recipientRoute,
        ));

        $this->replyForm->model($engagement)->saveRelationships();

        Notification::make()
            ->title(($data['send_later'] ?? false) ? 'Reply scheduled!' : 'Reply sent!')
            ->success()
            ->send();

        redirect(Inbox::getUrl());
    }

    public function getInvertedStatus(): EngagementResponseStatus
    {
        return match ($this->record->status) {
            EngagementResponseStatus::New => EngagementResponseStatus::Actioned,
            EngagementResponseStatus::Actioned => EngagementResponseStatus::New,
        };
    }

    public function changeStatus(): void
    {
        $this->record->status = $this->getInvertedStatus();
        $this->record->save();
    }

    protected function generateEmailReplyBody(string $content = '<p></p>'): string
    {
        assert($this->record->sender instanceof Student || $this->record->sender instanceof Prospect);

        return "{$content} <hr /> From: {$this->record->sender->full_name} &lt;{$this->record->sender->primaryEmailAddress?->address}&gt; <br /> Date: {$this->record->sent_at->toDayDateTimeString()} <br /> <br /> {$this->record->content}";
    }

    protected function getHeaderActions(): array
    {
        return [
            SendEngagementAction::make()
                ->label('New')
                ->icon(null),
        ];
    }
}
