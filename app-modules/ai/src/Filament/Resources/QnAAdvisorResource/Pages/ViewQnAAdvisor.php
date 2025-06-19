<?php

namespace AdvisingApp\Ai\Filament\Resources\QnAAdvisorResource\Pages;

use AdvisingApp\Ai\Filament\Resources\QnAAdvisorResource;
use AdvisingApp\Ai\Models\QnAAdvisor;
use Filament\Actions\Action;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\SpatieMediaLibraryImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Str;

class ViewQnAAdvisor extends ViewRecord
{
    protected static string $resource = QnAAdvisorResource::class;

    protected static ?string $navigationLabel = 'View';

    protected static ?string $navigationGroup = 'QnA Advisor';

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Section::make()->schema([
                SpatieMediaLibraryImageEntry::make('avatar')
                    ->visibility('private')
                    ->collection('avatar')
                    ->circular(),
                TextEntry::make('name'),
                TextEntry::make('description'),
                TextEntry::make('model'),
            ]),
        ]);
    }

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

    protected function getHeaderActions(): array
    {
        return [
            Action::make('archive')
                ->color('danger')
                ->action(function () {
                    $assistant = $this->getRecord();
                    $assistant->archived_at = now();
                    $assistant->save();

                    Notification::make()
                        ->title('QnA Advisor archived')
                        ->success()
                        ->send();
                })
                ->hidden(fn (): bool => (bool) $this->getRecord()->archived_at),
            Action::make('restore')
                ->action(function () {
                    $assistant = $this->getRecord();
                    $assistant->archived_at = null;
                    $assistant->save();

                    Notification::make()
                        ->title('QnA Advisor restored')
                        ->success()
                        ->send();
                })
                ->hidden(function (): bool {
                    if (! $this->getRecord()->archived_at) {
                        return true;
                    }

                    return false;
                }),
        ];
    }
}
