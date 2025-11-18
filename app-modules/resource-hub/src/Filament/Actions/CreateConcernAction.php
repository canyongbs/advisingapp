<?php

namespace AdvisingApp\ResourceHub\Filament\Actions;

use AdvisingApp\ResourceHub\Enums\ConcernStatus;
use AdvisingApp\ResourceHub\Models\ResourceHubArticle;
use AdvisingApp\ResourceHub\Models\ResourceHubArticleConcern;
use App\Features\ResourceHubArticleConcernFeature;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;

class CreateConcernAction extends Action
{
    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label('Raise Concern')
            ->button()
            ->modalDescription('Please articulate the concern you have with this resource hub article. You may enter up to 100 characters in the box below.')
            ->schema([
                Textarea::make('description')
                    ->hiddenLabel()
                    ->maxLength(100)
                    ->required(),
            ])
            ->action(function (array $data, ResourceHubArticle $record): void {
                ResourceHubArticleConcern::create([
                    'description' => $data['description'],
                    'created_by_id' => auth()->id(),
                    'status' => ConcernStatus::New,
                    'resource_hub_article_id' => $record->getKey(),
                ]);
            })
            ->visible(ResourceHubArticleConcernFeature::active());
    }

    public static function getDefaultName(): ?string
    {
        return 'raiseConcern';
    }
}
