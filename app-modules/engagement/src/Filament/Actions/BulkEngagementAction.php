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

namespace AdvisingApp\Engagement\Filament\Actions;

use AdvisingApp\Engagement\Actions\CreateEngagementBatch;
use AdvisingApp\Engagement\DataTransferObjects\EngagementBatchCreationData;
use AdvisingApp\Engagement\DataTransferObjects\EngagementCreationData;
use AdvisingApp\Engagement\Filament\Forms\Components\EngagementSmsBodyInput;
use AdvisingApp\Engagement\Models\EmailTemplate;
use AdvisingApp\Notification\Enums\NotificationChannel;
use AdvisingApp\Notification\Models\Contracts\CanBeNotified;
use App\Features\EngagementsFeature;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Wizard\Step;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Tables\Actions\BulkAction;
use FilamentTiptapEditor\TiptapEditor;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Expression;
use Illuminate\Support\Collection;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class BulkEngagementAction
{
    public static function make(string $context)
    {
        return BulkAction::make('engage')
            ->icon('heroicon-o-chat-bubble-bottom-center-text')
            ->modalHeading('Send Bulk Engagement')
            ->modalDescription(fn (Collection $records) => "You have selected {$records->count()} {$context} to engage.")
            ->steps([
                Step::make('Choose your delivery method')
                    ->description('Select email or sms.')
                    ->schema([
                        Select::make('channel')
                            ->label('How would you like to send this engagement?')
                            ->options(NotificationChannel::getEngagementOptions())
                            ->default(NotificationChannel::Email->value)
                            ->disableOptionWhen(fn (string $value): bool => NotificationChannel::tryFrom($value)?->getCaseDisabled())
                            ->selectablePlaceholder(false)
                            ->live(),
                    ]),
                Step::make('Engagement Details')
                    ->description("Add the details that will be sent to the selected {$context}")
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
                            ->showMergeTagsInBlocksPanel(false)
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
                        EngagementSmsBodyInput::make(context: 'create'),
                        Actions::make([
                            BulkDraftWithAiAction::make()
                                ->mergeTags($mergeTags),
                        ]),
                    ]),
                Step::make('Schedule')
                    ->description('Choose when you would like to send this engagement.')
                    ->schema([
                        Toggle::make('send_later')
                            ->reactive()
                            ->helperText('By default, this email or text will send as soon as it is created unless you schedule it to send later.'),
                        DateTimePicker::make('deliver_at')
                            ->required()
                            ->visible(fn (Get $get) => $get('send_later')),
                    ]),
            ])
            ->action(function (Collection $records, array $data, Form $form) {
                if (EngagementsFeature::active()) {
                    $channel = NotificationChannel::parse($data['channel']);

                    app(CreateEngagementBatch::class)->execute(new EngagementCreationData(
                        user: auth()->user(),
                        recipient: ($channel === NotificationChannel::Sms) ? $records->filter(fn (CanBeNotified $record) => $record->canRecieveSms()) : $records,
                        channel: $channel,
                        subject: $data['subject'] ?? null,
                        body: $data['body'] ?? null,
                        temporaryBodyImages: array_map(
                            fn (TemporaryUploadedFile $file): array => [
                                'extension' => $file->getClientOriginalExtension(),
                                'path' => (fn () => $this->path)->call($file),
                            ],
                            $form->getFlatFields()['body']->getTemporaryImages(),
                        ),
                        scheduledAt: ($data['send_later'] ?? false) ? ($data['deliver_at'] ?? null) : null,
                    ));
                } else {
                    CreateEngagementBatch::dispatch(EngagementBatchCreationData::from([
                        'user' => auth()->user(),
                        'records' => $records->filter(function ($record) {
                            return $record->canRecieveSms();
                        }),
                        'channel' => $data['channel'],
                        'subject' => $data['subject'] ?? null,
                        'body' => $data['body'] ?? null,
                        'temporaryBodyImages' => array_map(
                            fn (TemporaryUploadedFile $file): array => [
                                'extension' => $file->getClientOriginalExtension(),
                                'path' => (fn () => $this->path)->call($file),
                            ],
                            $form->getFlatFields()['body']->getTemporaryImages(),
                        ),
                    ]));
                }
            })
            ->modalSubmitActionLabel('Send')
            ->deselectRecordsAfterCompletion()
            ->modalCloseButton(false)
            ->closeModalByClickingAway(false)
            ->closeModalByEscaping(false)
            ->modalCancelAction(false)
            ->extraModalFooterActions([
                Action::make('cancel')
                    ->color('gray')
                    ->cancelParentActions()
                    ->requiresConfirmation()
                    ->action(fn () => null)
                    ->modalSubmitAction(fn (Action $action) => $action->color('danger')),
            ]);
    }
}
