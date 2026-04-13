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

namespace AdvisingApp\Campaign\Filament\Blocks;

use AdvisingApp\Campaign\Filament\Blocks\Actions\DraftEngagementBlockWithAi;
use AdvisingApp\Campaign\Filament\Forms\Components\CampaignDateTimeInput;
use AdvisingApp\Campaign\Models\CampaignAction;
use AdvisingApp\Engagement\Models\EmailTemplate;
use AdvisingApp\Engagement\Models\Engagement;
use AdvisingApp\Notification\Enums\NotificationChannel;
use Filament\Actions\Action;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\RichEditor\ToolbarButtonGroup;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Expression;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class EngagementBatchEmailBlock extends CampaignActionBlock
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->label('Email');

        $this->model(CampaignAction::class);

        $this->schema($this->generateFields());
    }

    public function generateFields(): array
    {
        return [
            Hidden::make('channel')
                ->default(NotificationChannel::Email->value),
            RichEditor::make('subject')
                ->label('Subject')
                ->toolbarButtons([])
                ->json()
                ->placeholder('Enter the email subject here...')
                ->required()
                ->helperText('You can insert recipient information by typing {{ and choosing a merge value to insert.')
                ->columnSpanFull(),
            RichEditor::make('body')
                ->fileAttachmentsDisk('s3-public')
                ->label('Body')
                ->toolbarButtons([
                    ['bold', 'italic', 'link'],
                    [ToolbarButtonGroup::make('Heading', ['h1', 'h2', 'h3', 'h4', 'h5', 'h6'])->textualButtons(), 'bulletList', 'orderedList', 'horizontalRule'],
                    ['textColor', 'small'],
                    ['attachFiles', 'mergeTags'],
                    ['clearFormatting'],
                    ['undo', 'redo'],
                ])
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
                ->saveFileAttachmentFromAnotherRecordUsing(function (mixed $file, ?Model $record): ?string {
                    if (! $record instanceof CampaignAction) {
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
                ->columnSpanFull()
                // Override the default saveRelationshipsUsing because CampaignAction stores
                // body inside a JSON `data` column. The default implementation calls
                // $record->setAttribute('body', ...) which fails since `body` is not a column.
                // This custom version saves to $record->data['body'] instead.
                ->saveRelationshipsUsing(function (RichEditor $component, ?array $rawState, CampaignAction $record): void {
                    $fileAttachmentProvider = $component->getFileAttachmentProvider();

                    if (! $fileAttachmentProvider) {
                        return;
                    }

                    if (! $fileAttachmentProvider->isExistingRecordRequiredToSaveNewFileAttachments()) {
                        return;
                    }

                    if (! $record->wasRecentlyCreated) {
                        return;
                    }

                    $fileAttachmentIds = [];

                    $component->rawState(
                        $component->getTipTapEditor()
                            ->setContent($rawState ?? [
                                'type' => 'doc',
                                'content' => [],
                            ])
                            ->descendants(function (object &$node) use ($component, &$fileAttachmentIds): void {
                                if ($node->type !== 'image') {
                                    return;
                                }

                                if (blank($node->attrs->id ?? null)) {
                                    return;
                                }

                                $attachment = $component->getUploadedFileAttachment($node->attrs->id);

                                if ($attachment) {
                                    $node->attrs->id = $component->saveUploadedFileAttachment($attachment);
                                    $node->attrs->src = $component->getFileAttachmentUrl($node->attrs->id);

                                    $fileAttachmentIds[] = $node->attrs->id;

                                    return;
                                }

                                if (filled($component->getFileAttachmentUrl($node->attrs->id))) {
                                    $fileAttachmentIds[] = $node->attrs->id;

                                    return;
                                }

                                $fileAttachmentIdFromAnotherRecord = $component->saveFileAttachmentFromAnotherRecord($node->attrs->id);

                                if (blank($fileAttachmentIdFromAnotherRecord)) {
                                    $fileAttachmentIds[] = $node->attrs->id;

                                    return;
                                }

                                $node->attrs->id = $fileAttachmentIdFromAnotherRecord;
                                $node->attrs->src = $component->getFileAttachmentUrl($fileAttachmentIdFromAnotherRecord) ?? $node->attrs->src ?? null;
                            })
                            ->getDocument(),
                    );

                    // Save body into the JSON `data` column instead of a direct `body` column
                    $data = $record->data ?? [];
                    $data['body'] = $component->getState();
                    $record->data = $data;
                    $record->save();

                    $fileAttachmentProvider->cleanUpFileAttachments(exceptIds: $fileAttachmentIds);
                }),
            Actions::make([
                DraftEngagementBlockWithAi::make()
                    ->channel(NotificationChannel::Email)
                    ->mergeTags(Engagement::getMergeTags(withUserTags: false)),
            ]),
            CampaignDateTimeInput::make(),
        ];
    }

    public static function type(): string
    {
        return 'bulk_engagement_email';
    }

    public function afterCreated(CampaignAction $action, Schema $schema): void
    {
        $schema->model($action)->saveRelationships();
    }
}
