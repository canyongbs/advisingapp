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

use AdvisingApp\Engagement\Actions\CreateEngagementBatch;
use AdvisingApp\Engagement\DataTransferObjects\EngagementCreationData;
use AdvisingApp\Engagement\Models\EmailTemplate;
use AdvisingApp\Engagement\Models\Engagement;
use AdvisingApp\Engagement\Models\EngagementBatch;
use AdvisingApp\Notification\Enums\NotificationChannel;
use AdvisingApp\Notification\Models\Contracts\CanBeNotified;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Expression;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class BulkEmailAction
{
    public static function make(string $context): BulkAction
    {
        return BulkAction::make('send_email')
            ->label('Send Email')
            ->icon('heroicon-o-envelope')
            ->modalHeading('Send Bulk Email')
            ->modalDescription(fn (Collection $records) => "You have selected {$records->count()} {$context} to send email.")
            ->model(EngagementBatch::class)
            ->steps([
                Step::make('Engagement Details')
                    ->description("Add the details that will be sent to the selected {$context}")
                    ->schema([
                        RichEditor::make('subject')
                            ->label('Subject')
                            ->toolbarButtons([])
                            ->json()
                            ->helperText('You may use "merge tags" to substitute information about a recipient into your subject line. Insert a "{{" in the subject line field to see a list of available merge tags')
                            ->required()
                            ->placeholder('Enter the email subject here...')
                            ->columnSpanFull(),
                        RichEditor::make('body')
                            ->fileAttachmentsDisk('s3-public')
                            ->label('Body')
                            ->toolbarButtons([['bold', 'italic', 'small', 'link', 'textColor'], ['h1', 'h2', 'h3', 'bulletList', 'orderedList', 'horizontalRule', 'attachFiles'], ['mergeTags']])
                            ->activePanel('mergeTags')
                            ->resizableImages()
                            ->json()
                            ->required()
                            ->hintAction(fn (RichEditor $component) => Action::make('loadEmailTemplate')
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
                                        })
                                        ->getOptionLabelUsing(function (Get $get, string|int|null $value): ?string {
                                            if (blank($value)) {
                                                return null;
                                            }

                                            return EmailTemplate::query()
                                                ->when(
                                                    $get('onlyMyTemplates'),
                                                    fn (Builder $query) => $query->whereBelongsTo(auth()->user())
                                                )
                                                ->when(
                                                    $get('onlyMyTeamTemplates'),
                                                    fn (Builder $query) => $query->whereIn('user_id', auth()->user()->team->users->pluck('id'))
                                                )
                                                ->whereKey($value)
                                                ->value('name');
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

                                    $component->state($template->content);
                                }))
                            ->getFileAttachmentUrlFromAnotherRecordUsing(function (mixed $file): ?string {
                                return Media::query()
                                    ->where('uuid', $file)
                                    ->where('model_type', (new EmailTemplate())->getMorphClass())
                                    ->first()
                                    ?->getUrl();
                            })
                            ->saveFileAttachmentFromAnotherRecordUsing(function (mixed $file, ?EngagementBatch $record): ?string {
                                if (! $record) {
                                    return null;
                                }

                                return Media::query()
                                    ->where('uuid', $file)
                                    ->where('model_type', (new EmailTemplate())->getMorphClass())
                                    ->first()
                                    ?->copy($record, 'body', 's3-public')
                                    ->uuid;
                            })
                            ->helperText('You can insert recipient information by typing {{ and choosing a merge value to insert.')
                            ->columnSpanFull(),
                        Actions::make([
                            BulkDraftWithAiAction::make()
                                ->mergeTags(Engagement::getMergeTags()),
                        ]),
                    ]),
                Step::make('Schedule')
                    ->description('Choose when you would like to send this engagement.')
                    ->schema([
                        Toggle::make('send_later')
                            ->reactive()
                            ->helperText('By default, this email will send as soon as it is created unless you schedule it to send later.'),
                        DateTimePicker::make('scheduled_at')
                            ->required()
                            ->visible(fn (Get $get) => $get('send_later')),
                    ]),
            ])
            ->action(function (Collection $records, array $data, Schema $schema) {
                /** @var Collection<int, CanBeNotified> $records */
                app(CreateEngagementBatch::class)->execute(new EngagementCreationData(
                    user: Auth::user(),
                    recipient: $records->filter(fn (CanBeNotified $record) => $record->canReceiveEmail()),
                    channel: NotificationChannel::Email,
                    subject: $data['subject'] ?? null,
                    body: $data['body'] ?? null,
                    scheduledAt: ($data['send_later'] ?? false) ? Carbon::parse($data['scheduled_at'] ?? null) : null,
                    schema: $schema,
                ));
            })
            ->modalSubmitActionLabel('Send')
            ->deselectRecordsAfterCompletion()
            ->modalCloseButton(false)
            ->closeModalByClickingAway(false)
            ->closeModalByEscaping(false);
    }
}
