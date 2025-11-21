<?php

namespace AdvisingApp\ResourceHub\Filament\Actions;

use AdvisingApp\ResourceHub\Enums\ConcernStatus;
use AdvisingApp\ResourceHub\Models\ResourceHubArticleConcern;
use AdvisingApp\ResourceHub\Notifications\ResourceHubArticleConcernStatusChanged;
use App\Features\ResourceHubArticleConcernFeature;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Widgets\Widget;

class ChangeConcernStatusAction extends Action
{
    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label('Change Status')
            ->button()
            ->outlined()
            ->modalDescription('Select what status this concern should have.')
            ->schema([
                Select::make('status')
                    ->options(ConcernStatus::class)
                    ->enum(ConcernStatus::class)
                    ->default(fn(ResourceHubArticleConcern $record) => $record->status->value),
            ])
            ->action(function (array $data, ResourceHubArticleConcern $record, Widget $livewire): void {
                $record->status = $data['status'];

                $record->save();

                $record->createdBy->notifyNow(new ResourceHubArticleConcernStatusChanged($record));
            })
            ->visible(ResourceHubArticleConcernFeature::active() && auth()->user()->can('resource_hub_article.*.update'));
    }

    public static function getDefaultName(): ?string
    {
        return 'changeConcernStatus';
    }
}
