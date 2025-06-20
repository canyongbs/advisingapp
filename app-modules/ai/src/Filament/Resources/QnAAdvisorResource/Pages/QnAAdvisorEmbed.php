<?php

namespace AdvisingApp\Ai\Filament\Resources\QnAAdvisorResource\Pages;

use AdvisingApp\Ai\Actions\Widgets\GenerateWidgetEmbedCode;
use AdvisingApp\Ai\Filament\Resources\QnAAdvisorResource;
use AdvisingApp\Ai\Models\QnAAdvisor;
use AdvisingApp\Form\Rules\IsDomain;
use App\Features\QnAAdvisorFeature;
use App\Filament\Resources\Pages\EditRecord\Concerns\EditPageRedirection;
use App\Models\User;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class QnAAdvisorEmbed extends EditRecord
{
    use EditPageRedirection;

    protected static string $resource = QnAAdvisorResource::class;

    protected static ?string $title = 'Embed';

    protected static ?string $navigationGroup = 'Configuration';

    protected static ?string $breadcrumb = 'Embed';

    /**
     * @return array<int|string, string|null>
     */
    public function getBreadcrumbs(): array
    {
        $resource = static::getResource();
        /** @var QnAAdvisor $record */
        $record = $this->getRecord();

        /** @var array<string, string> $breadcrumbs */
        $breadcrumbs = [
            $resource::getUrl() => $resource::getBreadcrumb(),
            $resource::getUrl('view', ['record' => $record]) => Str::limit($record->name, 16),
            ...(filled($breadcrumb = $this->getBreadcrumb()) ? [$breadcrumb] : []),
        ];

        if (filled($cluster = static::getCluster())) {
            return $cluster::unshiftClusterBreadcrumbs($breadcrumbs);
        }

        return $breadcrumbs;
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Embed QnA Advisor')
                    ->schema([
                        Toggle::make('is_enabled')
                            ->label('Enable Embed')
                            ->live(),
                        TagsInput::make('allowed_domains')
                            ->label('Authorized Domains')
                            ->helperText('Only these domains will be allowed to embed this form.')
                            ->placeholder('example.com')
                            ->hidden(fn (Get $get) => ! $get('is_enabled'))
                            ->disabled(fn (Get $get) => ! $get('is_enabled'))
                            ->nestedRecursiveRules(
                                [
                                    'string',
                                    new IsDomain(),
                                ]
                            ),
                        Actions::make([
                            Action::make('embed_snippet')
                                ->label('Embed Snippet')
                                ->infolist(
                                    [
                                        TextEntry::make('snippet')
                                            ->label('Click to Copy')
                                            ->state(function () {
                                                $code = resolve(GenerateWidgetEmbedCode::class)->handle($this->getRecord());

                                                $state = <<<EOD
                                                ```
                                                {$code}
                                                ```
                                                EOD;

                                                return str($state)->markdown()->toHtmlString();
                                            })
                                            ->copyable()
                                            ->copyableState(fn () => resolve(GenerateWidgetEmbedCode::class)->handle($this->getRecord()))
                                            ->copyMessage('Copied!')
                                            ->copyMessageDuration(1500)
                                            ->extraAttributes(['class' => 'embed-code-snippet']),
                                    ]
                                )
                                ->modalSubmitAction(false)
                                ->modalCancelActionLabel('Close')
                                ->visible(fn (Get $get) => $get('is_enabled')),
                        ]),
                    ]),
            ]);
    }

    /**
     * Get the owner record.
     *
     * @return QnAAdvisor|null
     */
    public function getOwnerRecord()
    {
        $recordId = $this->record->getKey() ?? null;

        if (is_null($recordId) || ! is_int($recordId) && ! is_string($recordId)) {
            return null;
        }

        return QnAAdvisor::find($recordId);
    }

    public function getRecord(): QnAAdvisor
    {
        /** @var QnAAdvisor $record */
        $record = parent::getRecord();

        return $record;
    }

    public static function canAccess(array $parameters = []): bool
    {
        /** @var User $user */
        $user = auth()->user();

        return QnAAdvisorFeature::active() && $user->can('qna_advisor_embed.view-any') && $user->can('qna_advisor_embed.*.view') && parent::canAccess($parameters);
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        /** @var QnAAdvisor $record */
        $record = $this->getRecord();

        if ($record->qnAAdvisorEmbed) {
            $data = $record->qnAAdvisorEmbed->toArray();
        }

        return $data;
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        /** @var QnAAdvisor $record */
        $record->qnAAdvisorEmbed()->updateOrCreate(
            [],
            $data
        );

        return $record;
    }
}
