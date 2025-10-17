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

namespace AdvisingApp\Ai\Filament\Resources\QnaAdvisors\Pages;

use AdvisingApp\Ai\Actions\GetQnaAdvisorInstructions;
use AdvisingApp\Ai\Filament\Resources\QnaAdvisors\QnaAdvisorResource;
use AdvisingApp\Ai\Models\QnaAdvisor;
use Filament\Actions\Action;
use Filament\Infolists\Components\SpatieMediaLibraryImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use UnitEnum;

class ViewQnaAdvisor extends ViewRecord
{
    protected static string $resource = QnaAdvisorResource::class;

    protected static ?string $navigationLabel = 'View';

    protected static string | UnitEnum | null $navigationGroup = 'QnA Advisor';

    public function infolist(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make()->schema([
                SpatieMediaLibraryImageEntry::make('avatar')
                    ->visibility('private')
                    ->collection('avatar')
                    ->circular(),
                TextEntry::make('name'),
                TextEntry::make('description'),
                Tabs::make('Generated Instructions')
                    ->tabs([
                        Tab::make('Generated Instructions Markdown')
                            ->icon('heroicon-o-document-text')
                            ->schema([
                                TextEntry::make('generated_instructions')
                                    ->hiddenLabel()
                                    ->columnSpanFull()
                                    ->html()
                                    ->extraAttributes(['class' => 'overflow-auto'])
                                    ->state(fn (QnaAdvisor $record): string => new HtmlString(
                                        '<pre>' . app(GetQnaAdvisorInstructions::class)->execute($record) . '</pre>'
                                    )),
                            ]),
                        Tab::make('Rendered')
                            ->hiddenLabel()
                            ->icon('heroicon-o-eye')
                            ->schema([
                                TextEntry::make('generated_instructions_preview')
                                    ->hiddenLabel()
                                    ->columnSpanFull()
                                    ->markdown()
                                    ->state(fn (QnaAdvisor $record): string => app(GetQnaAdvisorInstructions::class)->execute($record)),
                            ]),
                    ])->visible(fn () => auth()->guard('web')->user()?->isSuperAdmin()),
            ]),
        ]);
    }

    /**
     * @return array<int|string, string|null>
     */
    public function getBreadcrumbs(): array
    {
        $resource = static::getResource();
        /** @var QnaAdvisor $record */
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
                    /** @var QnaAdvisor $record */
                    $record = $this->getRecord();
                    $record->archived_at = now();
                    $record->save();

                    Notification::make()
                        ->title('QnA Advisor archived')
                        ->success()
                        ->send();
                })
                ->hidden(fn (QnaAdvisor $record): bool => (bool) $record->archived_at),
            Action::make('restore')
                ->action(function () {
                    /** @var QnaAdvisor $record */
                    $record = $this->getRecord();
                    $record->archived_at = null;
                    $record->save();

                    Notification::make()
                        ->title('QnA Advisor restored')
                        ->success()
                        ->send();
                })
                ->hidden(function (QnaAdvisor $record): bool {
                    if (! $record->archived_at) {
                        return true;
                    }

                    return false;
                }),
        ];
    }
}
