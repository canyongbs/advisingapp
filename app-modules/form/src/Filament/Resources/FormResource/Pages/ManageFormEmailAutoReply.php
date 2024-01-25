<?php

namespace AdvisingApp\Form\Filament\Resources\FormResource\Pages;

use App\Models\User;
use Filament\Forms\Get;
use Filament\Forms\Set;
use AdvisingApp\Form\Models\Form;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Section;
use FilamentTiptapEditor\TiptapEditor;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form as FilamentForm;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Expression;
use FilamentTiptapEditor\Enums\TiptapOutput;
use Filament\Forms\Components\Actions\Action;
use AdvisingApp\Engagement\Models\EmailTemplate;
use AdvisingApp\Form\Filament\Resources\FormResource;

class ManageFormEmailAutoReply extends EditRecord
{
    protected static string $resource = FormResource::class;

    protected static ?string $navigationLabel = 'Email Auto Reply';

    protected static ?string $navigationIcon = 'heroicon-o-envelope';

    protected static ?string $breadcrumb = 'Email Auto Reply';

    protected ?string $heading = 'Email Auto Reply';

    public static function canAccess(array $parameters = []): bool
    {
        /** @var ?Form $form */
        $form = $parameters['record'] ?? null;

        if(! $form) {
            return false;
        }

        return $form->is_authenticated;
    }

    public function form(FilamentForm $form): FilamentForm
    {
        return $form
            ->schema([
                Section::make()
                    ->relationship('emailAutoReply')
                    ->schema([
                        Toggle::make('is_enabled')
                            ->label('Enabled')
                            ->live(),
                        TextInput::make('subject')
                            ->required(fn (Get $get) => $get('is_enabled'))
                            ->placeholder('Subject')
                            ->columnSpanFull(),
                        TiptapEditor::make('body')
                            ->disk('s3-public')
                            ->visibility('public')
                            ->directory('editor-images/engagements')
                            ->mergeTags([
                                'student full name',
                                'student email',
                            ])
                            ->profile('email')
                            ->output(TiptapOutput::Json)
                            ->required(fn (Get $get) => $get('is_enabled'))
                            ->hintAction(fn (TiptapEditor $component) => Action::make('loadEmailTemplate')
                                ->form([
                                    Select::make('emailTemplate')
                                        ->searchable()
                                        ->options(function (Get $get): array {
                                            /** @var User $user */
                                            $user = auth()->user();

                                            return EmailTemplate::query()
                                                ->when(
                                                    $get('onlyMyTemplates'),
                                                    fn (Builder $query) => $query->whereBelongsTo($user)
                                                )
                                                ->orderBy('name')
                                                ->limit(50)
                                                ->pluck('name', 'id')
                                                ->toArray();
                                        })
                                        ->getSearchResultsUsing(function (Get $get, string $search): array {
                                            /** @var User $user */
                                            $user = auth()->user();

                                            return EmailTemplate::query()
                                                ->when(
                                                    $get('onlyMyTemplates'),
                                                    fn (Builder $query) => $query->whereBelongsTo($user)
                                                )
                                                ->when(
                                                    $get('onlyMyTeamTemplates'),
                                                    fn (Builder $query) => $query->whereIn('user_id', $user->teams->users->pluck('id'))
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

                                    $component->state($template->content);
                                }))
                            ->helperText('You can insert student information by typing {{ and choosing a merge value to insert.')
                            ->columnSpanFull()
                            ->live(),
                    ]),
            ]);
    }
}
