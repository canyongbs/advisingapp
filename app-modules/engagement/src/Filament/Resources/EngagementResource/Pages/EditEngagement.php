<?php

/*
<COPYRIGHT>

    Copyright © 2022-2023, Canyon GBS LLC. All rights reserved.

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

namespace Assist\Engagement\Filament\Resources\EngagementResource\Pages;

use Assist\Engagement\Enums\EngagementDeliveryMethod;
use Assist\Engagement\Models\EmailTemplate;
use Assist\Engagement\Models\Engagement;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Actions\DeleteAction;
use Assist\Prospect\Models\Prospect;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Pages\Page;
use Filament\Resources\Pages\EditRecord;
use Assist\AssistDataModel\Models\Student;
use Filament\Forms\Components\MorphToSelect;
use Filament\Forms\Components\DateTimePicker;
use Assist\Engagement\Filament\Resources\EngagementResource;
use FilamentTiptapEditor\Enums\TiptapOutput;
use FilamentTiptapEditor\TiptapEditor;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Expression;

class EditEngagement extends EditRecord
{
    protected static string $resource = EngagementResource::class;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('subject')
                    ->autofocus()
                    ->required()
                    ->placeholder(__('Subject'))
                    ->columnSpanFull(),
                TiptapEditor::make('body_json')
                    ->label('Body')
                    ->mergeTags([
                        'student full name',
                        'student email',
                    ])
                    ->profile('email')
                    ->output(TiptapOutput::Json)
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
                                        ->where(new Expression('lower(name)'), 'like', "%{$search}%")
                                        ->orderBy('name')
                                        ->limit(50)
                                        ->pluck('name', 'id')
                                        ->toArray();
                                }),
                            Checkbox::make('onlyMyTemplates')
                                ->label('Only show my templates')
                                ->live()
                                ->afterStateUpdated(fn (Set $set) => $set('emailTemplate', null))
                        ])
                        ->action(function (array $data) use ($component) {
                            $template = EmailTemplate::find($data['emailTemplate']);

                            if (! $template) {
                                return;
                            }

                            $component->state($template->content);
                        }))
                    ->visible(fn (Engagement $record): bool => filled($record->body_json))
                    ->showMergeTagsInBlocksPanel($form->getLivewire() instanceof Page)
                    ->helperText('You can insert student information by typing {{ and choosing a tag to insert.')
                    ->columnSpanFull(),
                Textarea::make('body')
                    ->placeholder('Body')
                    ->required()
                    ->maxLength(320) // https://www.twilio.com/docs/glossary/what-sms-character-limit#:~:text=Twilio's%20platform%20supports%20long%20messages,best%20deliverability%20and%20user%20experience.
                    ->helperText('The body of your message can be up to 320 characters long.')
                    ->visible(fn (Engagement $record): bool => blank($record->body_json))
                    ->columnSpanFull(),
                MorphToSelect::make('recipient')
                    ->label('Recipient')
                    ->searchable()
                    ->required()
                    ->types([
                        MorphToSelect\Type::make(Student::class)
                            ->titleAttribute(Student::displayNameKey()),
                        MorphToSelect\Type::make(Prospect::class)
                            ->titleAttribute(Prospect::displayNameKey()),
                    ]),
                Fieldset::make('Send your engagement')
                    ->schema([
                        Toggle::make('send_later')
                            ->reactive()
                            ->helperText('By default, this engagement will send as soon as it is created unless you schedule it to send later.'),
                        DateTimePicker::make('deliver_at')
                            ->required()
                            ->visible(fn (callable $get) => $get('send_later')),
                    ]),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
