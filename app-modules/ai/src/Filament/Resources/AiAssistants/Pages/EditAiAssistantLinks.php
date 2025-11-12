<?php

namespace AdvisingApp\Ai\Filament\Resources\AiAssistants\Pages;

use AdvisingApp\Ai\Filament\Resources\AiAssistants\AiAssistantResource;
use AdvisingApp\Ai\Models\AiAssistantLink;
use AdvisingApp\Ai\Models\QnaAdvisor;
use App\Filament\Resources\Pages\EditRecord\Concerns\EditPageRedirection;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\EditRecord;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Alignment;
use Illuminate\Support\Str;
use UnitEnum;

class EditAiAssistantLinks extends EditRecord
{
    use EditPageRedirection;

    protected static string $resource = AiAssistantResource::class;

    protected static ?string $title = 'Websites';

    protected static ?string $navigationLabel = 'Websites';

    protected static string | UnitEnum | null $navigationGroup = 'Configuration';

    public function getBreadcrumbs(): array
    {
        $resource = static::getResource();
        /** @var QnaAdvisor $record */
        $record = $this->getRecord();

        /** @var array<string, string> $breadcrumbs */
        $breadcrumbs = [
            $resource::getUrl() => $resource::getBreadcrumb(),
            //            $resource::getUrl('view', ['record' => $record]) => Str::limit($record->name, 16),
            ...(filled($breadcrumb = $this->getBreadcrumb()) ? [$breadcrumb] : []),
        ];

        if (filled($cluster = static::getCluster())) {
            return $cluster::unshiftClusterBreadcrumbs($breadcrumbs);
        }

        return $breadcrumbs;
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Repeater::make('links')
                    ->schema([
                        TextInput::make('url')
                            ->label('URL')
                            ->required()
                            ->disabled(fn (?AiAssistantLink $record): bool => $record !== null)
                            ->url(),
                    ])
                    ->relationship()
                    ->hiddenLabel()
                    ->addActionLabel('Add website')
                    ->addActionAlignment(Alignment::Start)
                    ->maxItems(25)
                    ->columnSpanFull(),
            ]);
    }

    public function getRedirectUrl(): ?string
    {
        return null;
    }
}
