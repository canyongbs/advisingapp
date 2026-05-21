<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS Inc. All rights reserved.

    Advising App® is licensed under the Elastic License 2.0. For more details,
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
    - Canyon GBS Inc. respects the intellectual property rights of others and expects the
      same in return. Canyon GBS® and Advising App® are registered trademarks of
      Canyon GBS Inc., and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS Inc.
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
use AdvisingApp\Engagement\Filament\Schemas\Components\EngagementEmailBodyInput;
use AdvisingApp\Engagement\Filament\Schemas\Components\EngagementScheduledAtDateTimePicker;
use AdvisingApp\Engagement\Filament\Schemas\Components\EngagementSendLaterToggle;
use AdvisingApp\Engagement\Filament\Schemas\Components\EngagementSubjectInput;
use AdvisingApp\Engagement\Models\Engagement;
use AdvisingApp\Notification\Enums\NotificationChannel;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\Prospect\Models\ProspectEmailAddress;
use AdvisingApp\Prospect\Models\ProspectPhoneNumber;
use AdvisingApp\StudentDataModel\Enums\EmailAddressOptInOptOutStatus;
use AdvisingApp\StudentDataModel\Models\Contracts\Educatable;
use AdvisingApp\StudentDataModel\Models\Student;
use AdvisingApp\StudentDataModel\Models\StudentEmailAddress;
use AdvisingApp\StudentDataModel\Models\StudentPhoneNumber;
use App\Filament\Forms\Components\EducatableSelect;
use Closure;
use Exception;
use Filament\Actions\Action;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Text;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Schemas\Schema;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class SendEngagementAction extends Action
{
    protected Student | Prospect | Closure | null $educatable = null;

    protected ?Closure $draftWithAiActionUsing = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->icon('heroicon-m-chat-bubble-bottom-center-text')
            ->slideOver()
            ->modalHeading('Send Message')
            ->modalDescription(function (): ?string {
                $educatable = $this->getEducatable();

                if (! $educatable) {
                    return null;
                }

                $educatableName = $educatable->getAttributeValue($educatable::displayNameKey());

                return "Send an engagement to {$educatableName}.";
            })
            ->model(Engagement::class)
            ->authorize(function () {
                $educatable = $this->getEducatable();

                return auth()->user()->can('create', [Engagement::class, $educatable instanceof Prospect ? $educatable : null]);
            })
            ->disabled(function (): bool {
                $educatable = $this->getEducatable();

                if (! $educatable) {
                    return false;
                }

                return ! $educatable->hasAnyValidContactRoute();
            })
            ->tooltip(function (): ?string {
                $educatable = $this->getEducatable();

                if (! $educatable) {
                    return null;
                }

                if (! $educatable->hasAnyValidContactRoute()) {
                    $label = $educatable::getLabel();

                    return "This {$label} does not have valid contact information in their record.";
                }

                return null;
            })
            ->mountUsing(function (array $arguments, Schema $schema) {
                $livewire = $this->getLivewire();

                if (method_exists($livewire, 'dispatch')) {
                    $livewire->dispatch('engage-action-finished-loading');
                }

                $educatable = $this->getEducatable();

                if (filled($arguments['route'] ?? null)) {
                    $schema->fill([
                        'channel' => $arguments['channel'] ?? 'email',
                        'recipient_route_id' => $arguments['route'],
                        'signature' => auth()->user()->signature,
                    ]);
                } else {
                    $defaultChannel = $educatable
                        ? $educatable->getDefaultEngagementChannel()
                        : null;

                    $schema->fill([
                        'channel' => $defaultChannel?->value,
                        'recipient_route_id' => ($educatable && $defaultChannel)
                            ? $educatable->getDefaultRouteForEngagementChannel($defaultChannel)
                            : null,
                        'signature' => auth()->user()->signature,
                    ]);
                }
            })
            ->steps(fn (): array => $this->getEngagementSteps())
            ->action(function (array $data, Schema $schema) {
                $this->createEngagement($data, $schema);
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

    public function educatable(Student | Prospect | Closure | null $educatable): static
    {
        $this->educatable = $educatable;

        return $this;
    }

    public function getEducatable(): Student | Prospect | null
    {
        if ($this->educatable instanceof Closure) {
            return ($this->educatable)();
        }

        return $this->educatable;
    }

    public function resolveEducatable(Get $get): Student | Prospect | null
    {
        return $this->getEducatable() ?? match ($get('recipient_type')) {
            'student' => Student::find($get('recipient_id')),
            'prospect' => Str::isUuid($get('recipient_id')) ? Prospect::find($get('recipient_id')) : null,
            default => null,
        };
    }

    public function draftWithAiAction(Closure $callback): static
    {
        $this->draftWithAiActionUsing = $callback;

        return $this;
    }

    /**
     * @return array<string>
     */
    public static function getDefaultMergeTags(): array
    {
        return Engagement::getMergeTags();
    }

    /**
     * @return array<Component>
     */
    protected function getEngagementSteps(): array
    {
        return [
            Step::make('Recipient Details')
                ->schema([
                    ...$this->getEducatable() ? [] : [
                        EducatableSelect::make(
                            name: 'recipient',
                            isExcludingConvertedProspects: true,
                            modifyKeySelectUsing: function (Select $select): Select {
                                return $select->disableOptionWhen(function (string $value): bool {
                                    static $noContactCache = [];
                                    $cacheKey = $value;

                                    if (! array_key_exists($cacheKey, $noContactCache)) {
                                        $educatable = Student::find($value) ?? Prospect::find($value);
                                        $noContactCache[$cacheKey] = $educatable
                                            ? ! $educatable->hasAnyValidContactRoute()
                                            : false;
                                    }

                                    return $noContactCache[$cacheKey];
                                });
                            },
                        )
                            ->label('Recipient Info')
                            ->live()
                            ->typeSelectToggleButtons()
                            ->required()
                            ->columns(2)
                            ->afterStateUpdated(function (Get $get, Set $set) {
                                $educatable = match ($get('recipient_type')) {
                                    'student' => Student::find($get('recipient_id')),
                                    'prospect' => Str::isUuid($get('recipient_id')) ? Prospect::find($get('recipient_id')) : null,
                                    default => null,
                                };

                                if (! $educatable) {
                                    return;
                                }

                                $defaultChannel = $educatable->getDefaultEngagementChannel();

                                if ($defaultChannel) {
                                    $set('channel', $defaultChannel->value);
                                    $set('recipient_route_id', $educatable->getDefaultRouteForEngagementChannel($defaultChannel));

                                    return;
                                }
                            }),
                    ],
                    Grid::make(2)
                        ->schema(function (Get $get): array {
                            return [
                                Fieldset::make('Message Type')
                                    ->schema([
                                        ToggleButtons::make('channel')
                                            ->options(function (Get $get): array {
                                                $educatable = $this->resolveEducatable($get);

                                                if (! $educatable) {
                                                    return [];
                                                }

                                                return array_filter(
                                                    NotificationChannel::getAvailableEngagementOptions(),
                                                    function (string $channelValue) use ($educatable): bool {
                                                        $channel = NotificationChannel::tryFrom($channelValue);

                                                        if ($channel?->getCaseDisabled() ?? false) {
                                                            return false;
                                                        }

                                                        return match ($channel) {
                                                            NotificationChannel::Email => $educatable->hasValidEmail(),
                                                            NotificationChannel::Sms => $educatable->hasValidSms(),
                                                            default => true,
                                                        };
                                                    },
                                                    ARRAY_FILTER_USE_KEY
                                                );
                                            })
                                            ->default(NotificationChannel::Email->value)
                                            ->inline()
                                            ->live()
                                            ->afterStateUpdated(function (mixed $state, Get $get, Set $set): void {
                                                $educatable = $this->resolveEducatable($get);

                                                if (! $educatable) {
                                                    return;
                                                }

                                                $channel = NotificationChannel::parse($state);
                                                $set('recipient_route_id', $educatable->getDefaultRouteForEngagementChannel($channel));
                                            }),
                                        Select::make('recipient_route_id')
                                            ->label(fn (Get $get): string => match (NotificationChannel::parse($get('channel'))) {
                                                NotificationChannel::Email => 'Email address',
                                                NotificationChannel::Sms => 'Phone number',
                                                default => throw new Exception('Invalid channel.'),
                                            })
                                            ->options(function (Get $get): array {
                                                $educatable = $this->resolveEducatable($get);

                                                if (! $educatable) {
                                                    return [];
                                                }

                                                return match (NotificationChannel::parse($get('channel'))) {
                                                    NotificationChannel::Email => $educatable->emailAddresses()
                                                        ->whereDoesntHave('bounced')
                                                        ->whereDoesntHave('optedOut', fn ($query) => $query->where('status', EmailAddressOptInOptOutStatus::OptedOut))
                                                        ->get()
                                                        ->mapWithKeys(fn (StudentEmailAddress | ProspectEmailAddress $emailAddress): array => [
                                                            $emailAddress->getKey() => $emailAddress->address . (filled($emailAddress->type) ? " ({$emailAddress->type})" : ''),
                                                        ])
                                                        ->all(),
                                                    NotificationChannel::Sms => $educatable->phoneNumbers()
                                                        ->where('can_receive_sms', true)
                                                        ->whereDoesntHave('smsOptOut')
                                                        ->whereDoesntHave('bounced')
                                                        ->get()
                                                        ->mapWithKeys(fn (StudentPhoneNumber | ProspectPhoneNumber $phoneNumber): array => [
                                                            $phoneNumber->getKey() => $phoneNumber->number . (filled($phoneNumber->ext) ? " (ext. {$phoneNumber->ext})" : '') . (filled($phoneNumber->type) ? " ({$phoneNumber->type})" : ''),
                                                        ])
                                                        ->all(),
                                                    default => [],
                                                };
                                            })
                                            ->disabled(function (Get $get): bool {
                                                $educatable = $this->resolveEducatable($get);

                                                return blank($educatable);
                                            })
                                            ->required(),
                                    ])
                                    ->visible(function (Get $get): bool {
                                        $educatable = $this->resolveEducatable($get);

                                        return $educatable !== null && $educatable->hasAnyValidContactRoute();
                                    })
                                    ->columnSpanFull(),
                                Text::make('This recipient does not have valid contact information. Please select a different recipient.')
                                    ->visible(function (Get $get): bool {
                                        $educatable = $this->resolveEducatable($get);

                                        return $educatable !== null && ! $educatable->hasAnyValidContactRoute();
                                    })
                                    ->columnSpanFull(),
                            ];
                        })
                        ->visible(fn (Get $get) => ! is_null($this->getEducatable()) || $get('recipient_id')),
                ]),
            Step::make('Message Details')
                ->schema(function (Get $get): array {
                    $educatable = $this->resolveEducatable($get);

                    return [
                        EngagementSubjectInput::make()
                            ->hidden(fn (Get $get): bool => $get('channel') === NotificationChannel::Sms->value),
                        EngagementEmailBodyInput::make()
                            ->hidden(fn (Get $get): bool => $get('channel') === NotificationChannel::Sms->value),
                        EngagementSmsBodyInput::make(context: 'create'),
                        ...$educatable ? [Actions::make([
                            $this->getDraftWithAiAction($educatable),
                        ])] : [],
                    ];
                }),
            Step::make('Email Signature')
                ->schema([
                    Toggle::make('is_signature_enabled')
                        ->label('Include Signature')
                        ->helperText('You may configure your email signature in Profile Settings by selecting your avatar in the upper right portion of the screen.')
                        ->live(),
                    RichEditor::make('signature')
                        ->toolbarButtons([['bold', 'italic', 'strike', 'underline', 'small', 'textColor', 'link', 'attachFiles']])
                        ->extraInputAttributes(['style' => 'min-height: 12rem;'])
                        ->json()
                        ->required(fn (Get $get) => $get('is_signature_enabled'))
                        ->fileAttachmentsDisk('s3-public')
                        ->visible(fn (Get $get) => $get('is_signature_enabled'))
                        ->default(auth()->user()->signature)
                        ->saveRelationshipsUsing(null),
                ])
                ->visible(auth()->user()->is_signature_enabled)
                ->hidden(fn (Get $get): bool => $get('channel') === NotificationChannel::Sms->value),
            Step::make('Delivery Details')
                ->schema([
                    EngagementSendLaterToggle::make(),
                    EngagementScheduledAtDateTimePicker::make(),
                ]),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     */
    protected function createEngagement(array $data, Schema $schema): void
    {
        /** @var Student | Prospect $recipient */
        $recipient = $this->getEducatable() ?? match ($data['recipient_type']) {
            'student' => Student::find($data['recipient_id']),
            'prospect' => Str::isUuid($data['recipient_id']) ? Prospect::find($data['recipient_id']) : null,
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

        $channel = NotificationChannel::parse($data['channel']);

        $recipientRoute = match ($channel) {
            NotificationChannel::Email => $recipient->emailAddresses()->find($data['recipient_route_id'] ?? null)?->address,
            NotificationChannel::Sms => $recipient->phoneNumbers()->find($data['recipient_route_id'] ?? null)?->number,
            default => null,
        };

        app(CreateEngagement::class)->execute(new EngagementCreationData(
            user: auth()->user(),
            recipient: $recipient,
            channel: $channel,
            subject: $data['subject'] ?? null,
            body: $data['body'] ?? null,
            scheduledAt: ($data['send_later'] ?? false) ? Carbon::parse($data['scheduled_at'] ?? null) : null,
            recipientRoute: $recipientRoute,
            schema: $schema,
        ));

        $this->getLivewire()->dispatch('engagement-sent');
    }

    protected function getDraftWithAiAction(Educatable $educatable): Action
    {
        if ($this->draftWithAiActionUsing) {
            return $this->evaluate($this->draftWithAiActionUsing);
        }

        return DraftWithAiAction::make()
            ->educatable($educatable)
            ->mergeTags(static::getDefaultMergeTags());
    }
}
