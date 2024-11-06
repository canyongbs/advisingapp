<?php

namespace AdvisingApp\Prospect\Filament\Resources\PipelineResource\Pages;

use Filament\Actions\EditAction;
use Filament\Infolists\Infolist;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\RepeatableEntry;
use AdvisingApp\Prospect\Filament\Resources\PipelineResource;
use AdvisingApp\Prospect\Models\Pipeline;
use Illuminate\Support\Str;

class ViewPipeline extends ViewRecord
{
    protected static string $resource = PipelineResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Section::make()->schema([
                TextEntry::make('name'),
                TextEntry::make('description'),
                TextEntry::make('segment.name'),
                RepeatableEntry::make('stages')
                    ->schema([
                        TextEntry::make('name')
                            ->label('Stage'),
                        IconEntry::make('is_default')
                            ->label('Is Default?')
                            ->boolean(),
                    ])
                    ->columns(2),
            ]),
        ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
            DeleteAction::make(),
        ];
    }

    /**
     * @return array<int|string, string|null>
     */
    public function getBreadcrumbs(): array
    {
        $resource = static::getResource();
        /** @var Pipeline $record */
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
}
