<?php

namespace Assist\Engagement\Filament\Resources\EngagementResource\Fields;

use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Form;
use Filament\Forms\Components\Field;
use Filament\Forms\Components\Select;
use FilamentTiptapEditor\TiptapEditor;
use Filament\Forms\Components\Checkbox;
use Assist\Engagement\Models\Engagement;
use Assist\Engagement\Models\SmsTemplate;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Expression;
use FilamentTiptapEditor\Enums\TiptapOutput;
use Filament\Forms\Components\Actions\Action;
use Assist\Engagement\Enums\EngagementDeliveryMethod;
use Filament\Resources\RelationManagers\RelationManager;

class EngagementSmsBodyField
{
    public static function make(string $context, ?Form $form = null)
    {
        // TODO Implement length validation (320 characters max)
        // https://www.twilio.com/docs/glossary/what-sms-character-limit#:~:text=Twilio's%20platform%20supports%20long%20messages,best%20deliverability%20and%20user%20experience.
        return TiptapEditor::make('body')
            ->label('Body')
            ->mergeTags([
                'student full name',
                'student email',
            ])
            ->showMergeTagsInBlocksPanel(is_null($form) ? false : ! ($form->getLivewire() instanceof RelationManager))
            ->profile('sms')
            ->output(TiptapOutput::Json)
            ->required()
            ->hintAction(fn (TiptapEditor $component) => Action::make('loadSmsTemplate')
                ->form([
                    Select::make('smsTemplate')
                        ->searchable()
                        ->options(function (Get $get): array {
                            return SmsTemplate::query()
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
                            return SmsTemplate::query()
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
                        ->afterStateUpdated(fn (Set $set) => $set('smsTemplate', null)),
                    Checkbox::make('onlyMyTeamTemplates')
                        ->label("Only show my team's templates")
                        ->live()
                        ->afterStateUpdated(fn (Set $set) => $set('smsTemplate', null)),
                ])
                ->action(function (array $data) use ($component) {
                    $template = SmsTemplate::find($data['smsTemplate']);

                    if (! $template) {
                        return;
                    }

                    $component->state($template->content);
                }))
            ->when($context === 'create', function (Field $field) {
                $field->hidden(fn (Get $get): bool => $get('delivery_method') === EngagementDeliveryMethod::Email->value);
            })
            ->when($context === 'edit', function (Field $field) {
                $field->visible(fn (Engagement $record): bool => $record->deliverable->channel === EngagementDeliveryMethod::Sms);
            })
            ->helperText('You can insert student information by typing {{ and choosing a tag to insert.')
            ->columnSpanFull();
    }
}
