<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS Inc. All rights reserved.

    Advising App® is licensed under the Elastic License 2.0. For more details,
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
    - Canyon GBS Inc. respects the intellectual property rights of others and expects the
      same in return. Canyon GBS® and Advising App® are registered trademarks of
      Canyon GBS Inc., and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS Inc.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace AdvisingApp\ResourceHub\Livewire;

use AdvisingApp\ResourceHub\Filament\Resources\ResourceHubQualities\ResourceHubQualityResource;
use AdvisingApp\ResourceHub\Models\ResourceHubQuality;
use App\Filament\Tables\Columns\IdColumn;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;

class ResourceHubQualitiesTable extends Component implements HasActions, HasForms, HasTable
{
    use InteractsWithActions;
    use InteractsWithForms;
    use InteractsWithTable;

    public function boot(): void
    {
        abort_unless(ResourceHubQualityResource::canViewAny(), 403);
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => ResourceHubQualityResource::getEloquentQuery())
            ->columns([
                IdColumn::make(),
                TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('resource_hub_articles_count')
                    ->label('# of Resource Hub Articles')
                    ->counts('resourceHubArticles')
                    ->sortable(),
            ])
            ->recordUrl(fn (ResourceHubQuality $record): ?string => match (true) {
                ResourceHubQualityResource::canView($record) => ResourceHubQualityResource::getUrl('view', ['record' => $record]),
                ResourceHubQualityResource::canEdit($record) => ResourceHubQualityResource::getUrl('edit', ['record' => $record]),
                default => null,
            })
            ->headerActions([
                CreateAction::make()
                    ->authorize('create', ResourceHubQuality::class)
                    ->url(fn (): string => ResourceHubQualityResource::getUrl('create')),
            ])
            ->recordActions([
                ViewAction::make()
                    ->authorize('view')
                    ->url(fn (ResourceHubQuality $record): string => ResourceHubQualityResource::getUrl('view', ['record' => $record])),
                EditAction::make()
                    ->authorize('update')
                    ->url(fn (ResourceHubQuality $record): string => ResourceHubQualityResource::getUrl('edit', ['record' => $record])),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->authorize('deleteAny', ResourceHubQuality::class)
                        ->authorizeIndividualRecords('delete'),
                ]),
            ]);
    }

    public function render(): View
    {
        return view('resource-hub::livewire.resource-hub-table');
    }
}
