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

namespace AdvisingApp\BasicNeeds\Filament\Actions;

use AdvisingApp\Engagement\Actions\CreateEngagement;
use AdvisingApp\Engagement\DataTransferObjects\EngagementCreationData;
use AdvisingApp\Engagement\Models\Engagement;
use AdvisingApp\Notification\Enums\NotificationChannel;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\Prospect\Models\ProspectEmailAddress;
use AdvisingApp\StudentDataModel\Models\Student;
use AdvisingApp\StudentDataModel\Models\StudentEmailAddress;
use Filament\Actions\Action;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Pages\Page;
use Filament\Schemas\Components\Grid;
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

class SendEmailAction extends Action
{
    protected Student|Prospect|null $educatable = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->icon('heroicon-m-chat-bubble-bottom-center-text')
            ->modalHeading('Send Engagement')
            ->model(Engagement::class)
            ->authorize(function () {
                return auth()->user()->can('create', Engagement::class);
            })
            ->steps(fn (): array => [
                Step::make('Contact Information')
                    ->schema([
                        Select::make('recipient_type')
                            ->label('Recipient Type')
                            ->options([
                                'student' => 'Student',
                                'prospect' => 'Prospect',
                            ])
                            ->live()
                            ->afterStateUpdated(fn (Set $set) => $set('recipient_id', null))
                            ->required(),
                        Select::make('recipient_id')
                            ->label('Recipient')
                            ->searchable()
                            ->hidden(fn (Get $get) => ! filled($get('recipient_type')))
                            ->options(function (Get $get): array {
                                $recipientType = $get('recipient_type');

                                if ($recipientType === 'student') {
                                    return Student::query()
                                        ->with('primaryEmailAddress')
                                        ->whereHas('primaryEmailAddress', fn ($query) => $query->whereDoesntHave('bounced'))
                                        ->limit(50)
                                        ->get()
                                        ->mapWithKeys(function (Student $student) {
                                            $label = "{$student->display_name} (Student) - {$student->primaryEmailAddress?->address} - {$student->sisid}";

                                            return [$student->sisid => $label];
                                        })
                                        ->toArray();
                                }

                                if ($recipientType === 'prospect') {
                                    return Prospect::query()
                                        ->with('primaryEmailAddress')
                                        ->whereHas('primaryEmailAddress', fn ($query) => $query->whereDoesntHave('bounced'))
                                        ->limit(50)
                                        ->get()
                                        ->mapWithKeys(function (Prospect $prospect) {
                                            $label = "{$prospect->display_name} (Prospect) - {$prospect->primaryEmailAddress?->address}";

                                            return [$prospect->id => $label];
                                        })
                                        ->toArray();
                                }

                                return [];
                            })
                            ->getSearchResultsUsing(function (string $search, Get $get): array {
                                $recipientType = $get('recipient_type');

                                if ($recipientType === 'student') {
                                    return Student::query()
                                        ->with('primaryEmailAddress')
                                        ->when($search, function (Builder $query) use ($search) {
                                            $query->where(new Expression('lower(full_name)'), 'like', "%{$search}%")
                                                ->orWhere(new Expression('lower(sisid)'), 'like', "%{$search}%")
                                                ->orWhereHas('primaryEmailAddress', fn (Builder $query) => $query->where(new Expression('lower(address)'), 'like', "%{$search}%"));
                                        })
                                        ->whereHas('primaryEmailAddress', fn ($query) => $query->whereDoesntHave('bounced'))
                                        ->limit(50)
                                        ->get()
                                        ->mapWithKeys(function (Student $student) {
                                            $label = "{$student->display_name} (Student) - {$student->primaryEmailAddress?->address} - {$student->sisid}";

                                            return [$student->sisid => $label];
                                        })
                                        ->toArray();
                                }

                                if ($recipientType === 'prospect') {
                                    return Prospect::query()
                                        ->with('primaryEmailAddress')
                                        ->when($search, function (Builder $query) use ($search) {
                                            $query->where(new Expression('lower(full_name)'), 'like', "%{$search}%")
                                                ->orWhereHas('primaryEmailAddress', fn (Builder $query) => $query->where(new Expression('lower(address)'), 'like', "%{$search}%"));
                                        })
                                        ->whereHas('primaryEmailAddress', fn ($query) => $query->whereDoesntHave('bounced'))
                                        ->limit(50)
                                        ->get()
                                        ->mapWithKeys(function (Prospect $prospect) {
                                            $label = "{$prospect->display_name} (Prospect) - {$prospect->primaryEmailAddress?->address}";

                                            return [$prospect->id => $label];
                                        })
                                        ->toArray();
                                }

                                return [];
                            })
                            ->getOptionLabelUsing(function (string $value, Get $get): string {
                                $recipientType = $get('recipient_type');

                                if ($recipientType === 'student') {
                                    $student = Student::query()->find($value);

                                    if (! $student) {
                                        return '';
                                    }

                                    return "{$student->display_name} (Student) - {$student->primaryEmailAddress?->address} - {$student->sisid}";
                                }

                                if ($recipientType === 'prospect') {
                                    $prospect = Prospect::query()->find($value);

                                    if (! $prospect) {
                                        return '';
                                    }

                                    return "{$prospect->display_name} (Prospect) - {$prospect->primaryEmailAddress?->address}";
                                }

                                return '';
                            })
                            ->afterStateUpdated(function (Get $get, Set $set) {
                                $educatable = match ($get('recipient_type')) {
                                    'student' => Student::find($get('recipient_id')),
                                    'prospect' => Prospect::find($get('recipient_id')),
                                    default => null,
                                };

                                $defaultBody = view('basic-needs::components.default-email-body', [
                                    'recipient' => $educatable,
                                    'record' => $this->getRecord(),
                                ])->render();

                                $set('body', $defaultBody);

                                $set('recipient_route_id', $educatable?->primaryEmailAddress?->getKey() ?? $educatable?->emailAddresses()->first()?->getKey());
                            })
                            ->live()
                            ->required(),

                        Grid::make(1)
                            ->schema(function (Get $get): array {
                                $educatable = match ($get('recipient_type')) {
                                    'student' => Student::find($get('recipient_id')),
                                    'prospect' => Prospect::find($get('recipient_id')),
                                    default => null,
                                };

                                return [
                                    Select::make('recipient_route_id')
                                        ->label('Email address')
                                        ->options(function () use ($educatable) {
                                            return $educatable?->emailAddresses
                                                ->mapWithKeys(fn (StudentEmailAddress|ProspectEmailAddress $emailAddress): array => [
                                                    $emailAddress->getKey() => $emailAddress->address . (filled($emailAddress->type) ? " ({$emailAddress->type})" : ''),
                                                ])
                                                ->all() ?? [];
                                        })
                                        ->disabled(blank($educatable))
                                        ->required(),
                                ];
                            })
                            ->hidden(fn (Get $get) => ! filled($get('recipient_type'))),
                    ]),
                Step::make('Content')
                    ->schema(function (Get $get): array {
                        $educatable = match ($get('recipient_type')) {
                            'student' => Student::find($get('recipient_id')),
                            'prospect' => Prospect::find($get('recipient_id')),
                            default => null,
                        };

                        return [
                            TiptapEditor::make('subject')
                                ->label('Subject')
                                ->showMergeTagsInBlocksPanel(false)
                                ->profile('email')
                                ->required()
                                ->placeholder('Enter the email subject here...')
                                ->columnSpanFull(),
                            TiptapEditor::make('body')
                                ->disk('s3-public')
                                ->label('Body')
                                ->profile('email')
                                ->default(function () use ($educatable) {
                                    return view('basic-needs::components.default-email-body', [
                                        'recipient' => $educatable,
                                        'record' => $this->getRecord(),
                                    ])->render();
                                })
                                ->required()
                                ->columnSpanFull(),
                        ];
                    }),
                Step::make('Email Signature')
                    ->schema([
                        Toggle::make('is_signature_enabled')
                            ->label('Include Signature')
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
                    ->visible(auth()->user()->is_signature_enabled),
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
            ->action(function (array $data, Schema $schema, Page $livewire) {
                /** @var Student | Prospect $recipient */
                $recipient = match ($data['recipient_type']) {
                    'student' => Student::find($data['recipient_id']),
                    'prospect' => Prospect::find($data['recipient_id']),
                    default => null,
                };
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

                /** @var TiptapEditor $bodyField */
                $bodyField = $formFields['body'] ?? null;

                /** @var ?TiptapEditor $signatureField */
                $signatureField = $formFields['signature'] ?? null;

                $channel = NotificationChannel::parse(NotificationChannel::Email->value);

                $recipientRoute = $recipient->emailAddresses()->find($data['recipient_route_id'] ?? null)?->address;

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
                            $bodyField->getTemporaryImages(),
                        ),
                        ...($signatureField ? array_map(
                            fn (TemporaryUploadedFile $file): array => [
                                'extension' => $file->getClientOriginalExtension(),
                                'path' => (fn () => $this->path)->call($file),
                            ],
                            $signatureField->getTemporaryImages(),
                        ) : []),
                    ],
                    scheduledAt: ($data['send_later'] ?? false) ? Carbon::parse($data['scheduled_at'] ?? null) : null,
                    recipientRoute: $recipientRoute,
                ));

                $schema->model($engagement)->saveRelationships();

                $livewire->dispatch('engagement-sent');
            })
            ->modalSubmitActionLabel('Send')
            ->modalCloseButton(false)
            ->closeModalByClickingAway(false)
            ->closeModalByEscaping(false);
    }

    public static function getDefaultName(): ?string
    {
        return 'engage';
    }
}
