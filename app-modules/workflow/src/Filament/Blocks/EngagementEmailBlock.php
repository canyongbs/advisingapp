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

namespace AdvisingApp\Workflow\Filament\Blocks;

use AdvisingApp\Campaign\Filament\Blocks\Actions\DraftEngagementBlockWithAi;
use AdvisingApp\Engagement\Filament\Actions\LoadEmailTemplateAction;
use AdvisingApp\Engagement\Models\EmailTemplate;
use AdvisingApp\Engagement\Models\Engagement;
use AdvisingApp\Notification\Enums\NotificationChannel;
use AdvisingApp\Workflow\Models\WorkflowDetails;
use AdvisingApp\Workflow\Models\WorkflowEngagementEmailDetails;
use Filament\Forms\Components\Field;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\RichEditor\ToolbarButtonGroup;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class EngagementEmailBlock extends WorkflowActionBlock
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->label('Email');

        $this->model(WorkflowEngagementEmailDetails::class);

        $this->schema($this->generateFields());
    }

    /**
     * @return array<int, covariant Field|Section|Actions>
     */
    public function generateFields(): array
    {
        return [
            Hidden::make('channel')
                ->default(NotificationChannel::Email->value),
            Hidden::make('email_type')
                ->default('transactional')
                ->dehydrated(),
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
                ->hintAction(LoadEmailTemplateAction::make())
                ->getFileAttachmentUrlFromAnotherRecordUsing(function (mixed $file): ?string {
                    return Media::query()
                        ->where('uuid', $file)
                        ->where('model_type', (new EmailTemplate())->getMorphClass())
                        ->first()
                        ?->getUrl();
                })
                ->saveFileAttachmentFromAnotherRecordUsing(function (mixed $file, ?Model $record): ?string {
                    if (! $record instanceof WorkflowEngagementEmailDetails) {
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
                DraftEngagementBlockWithAi::make()
                    ->channel(NotificationChannel::Email)
                    ->mergeTags(Engagement::getMergeTags(withUserTags: false)),
            ]),
            Section::make('How long after the previous step should this occur?')
                ->schema([
                    TextInput::make('days')
                        ->translateLabel()
                        ->numeric()
                        ->step(1)
                        ->minValue(0)
                        ->default(0)
                        ->inlineLabel(),
                    TextInput::make('hours')
                        ->translateLabel()
                        ->numeric()
                        ->step(1)
                        ->minValue(0)
                        ->default(0)
                        ->inlineLabel(),
                    TextInput::make('minutes')
                        ->translateLabel()
                        ->numeric()
                        ->step(1)
                        ->minValue(0)
                        ->default(0)
                        ->inlineLabel(),
                ])
                ->columns(3),
        ];
    }

    public static function type(): string
    {
        return 'workflow_engagement_email_details';
    }

    public function afterCreated(WorkflowDetails $details, Schema $schema): void
    {
        if (! ($details instanceof WorkflowEngagementEmailDetails)) {
            return;
        }

        $schema->model($details)->saveRelationships();
    }
}
