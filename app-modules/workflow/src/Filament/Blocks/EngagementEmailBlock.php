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

namespace AdvisingApp\Workflow\Filament\Blocks;

use AdvisingApp\Campaign\Filament\Blocks\Actions\DraftEngagementBlockWithAi;
use AdvisingApp\Engagement\Models\EmailTemplate;
use AdvisingApp\Notification\Enums\NotificationChannel;
use AdvisingApp\Workflow\Models\WorkflowDetails;
use AdvisingApp\Workflow\Models\WorkflowEngagementEmailDetails;
use Filament\Forms\ComponentContainer;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Field;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Get;
use Filament\Forms\Set;
use FilamentTiptapEditor\TiptapEditor;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Expression;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class EngagementEmailBlock extends WorkflowActionBlock
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->label('Email');

        $this->schema($this->createFields());
    }

    /**
     * @return array<int, covariant Field|Section|Actions>
     */
    public function generateFields(string $fieldPrefix = ''): array
    {
        return [
            Hidden::make('channel')
                ->default(NotificationChannel::Email->value),
            TiptapEditor::make('subject')
                ->label('Subject')
                ->mergeTags($mergeTags = [
                    'recipient first name',
                    'recipient last name',
                    'recipient full name',
                    'recipient email',
                    'recipient preferred name',
                ])
                ->profile('sms')
                ->placeholder('Enter the email subject here...')
                ->showMergeTagsInBlocksPanel(false)
                ->required()
                ->helperText('You can insert recipient information by typing {{ and choosing a merge value to insert.')
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
                ])
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
                ->helperText('You can insert recipient information by typing {{ and choosing a merge value to insert.')
                ->columnSpanFull(),
            Actions::make([
                DraftEngagementBlockWithAi::make()
                    ->channel(NotificationChannel::Email)
                    ->fieldPrefix($fieldPrefix)
                    ->mergeTags($mergeTags),
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

    public function afterCreated(WorkflowDetails $details, ComponentContainer $componentContainer): void
    {
        if (! ($details instanceof WorkflowEngagementEmailDetails)) {
            return;
        }

        $bodyField = $componentContainer->getComponent(fn (Component $component): bool => ($component instanceof TiptapEditor) && str($component->getName())->endsWith('body'));

        if (! ($bodyField instanceof TiptapEditor)) {
            return;
        }

        [$newBody] = tiptap_converter()->saveImages(
            $details->body,
            disk: 's3-public',
            record: $details,
            recordAttribute: 'body',
            newImages: array_map(
                fn (TemporaryUploadedFile $file): array => [
                    'extension' => $file->getClientOriginalExtension(),
                    'path' => (fn () => $this->path)->call($file),
                ],
                $bodyField->getTemporaryImages(),
            ),
        );

        $details->body = $newBody;
        $details->save();
    }
}
