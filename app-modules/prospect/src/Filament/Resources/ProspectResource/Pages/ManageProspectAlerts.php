<?php

/*
<COPYRIGHT>

    Copyright © 2016-2024, Canyon GBS LLC. All rights reserved.

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

namespace AdvisingApp\Prospect\Filament\Resources\ProspectResource\Pages;

use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Infolists\Infolist;
use AdvisingApp\Alert\Models\Alert;
use Filament\Forms\Components\Select;
use Illuminate\Support\Facades\Cache;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use AdvisingApp\Prospect\Models\Prospect;
use App\Filament\Tables\Columns\IdColumn;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use AdvisingApp\Alert\Enums\AlertSeverity;
use Filament\Infolists\Components\TextEntry;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Resources\Pages\ManageRelatedRecords;
use AdvisingApp\Prospect\Concerns\ProspectHolisticViewPage;
use AdvisingApp\Alert\Enums\SystemAlertStatusClassification;
use AdvisingApp\Prospect\Filament\Resources\ProspectResource;

class ManageProspectAlerts extends ManageRelatedRecords
{
    use ProspectHolisticViewPage;

    protected static string $resource = ProspectResource::class;

    protected static string $relationship = 'alerts';

    // TODO: Automatically set from Filament based on relationship name
    protected static ?string $navigationLabel = 'Alerts';

    // TODO: Automatically set from Filament based on relationship name
    protected static ?string $breadcrumb = 'Alerts';

    protected static ?string $navigationIcon = 'heroicon-o-bell-alert';

    public static function getNavigationItems(array $urlParameters = []): array
    {
        $item = parent::getNavigationItems($urlParameters)[0];

        $ownerRecord = $urlParameters['record'];

        /** @var Prospect $ownerRecord */
        $alertsCount = Cache::tags('alert-count')
            ->remember(
                "alert-count-{$ownerRecord->getKey()}",
                now()->addMinutes(5),
                function () use ($ownerRecord): int {
                    return $ownerRecord->alerts()->whereRelation('status', 'classification', SystemAlertStatusClassification::Active->value)->count();
                },
            );

        $item->badge($alertsCount > 0 ? $alertsCount : null, color: 'danger');

        return [$item];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                TextEntry::make('description'),
                TextEntry::make('severity'),
                TextEntry::make('suggested_intervention'),
                TextEntry::make('status.name'),
                TextEntry::make('createdBy.name')->label('Created By')->default('N/A'),
                TextEntry::make('created_at')->label('Created Date'),
            ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Textarea::make('description')
                    ->required()
                    ->string(),
                Select::make('severity')
                    ->options(AlertSeverity::class)
                    ->selectablePlaceholder(false)
                    ->default(AlertSeverity::default())
                    ->required()
                    ->enum(AlertSeverity::class),
                Textarea::make('suggested_intervention')
                    ->required()
                    ->string(),
                Select::make('status_id')
                    ->label('Status')
                    ->relationship('status', 'name', fn (Builder $query) => $query->orderBy('order'))
                    ->default(fn () => SystemAlertStatusClassification::default()?->getKey())
                    ->selectablePlaceholder(false)
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('description')
            ->columns([
                IdColumn::make(),
                TextColumn::make('description')
                    ->limit(),
                TextColumn::make('severity')
                    ->sortable(),
                TextColumn::make('status.name')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('severity')
                    ->options(AlertSeverity::class),
                SelectFilter::make('status_id')
                    ->relationship('status', 'name', fn (Builder $query) => $query->orderBy('order')),
            ])
            ->headerActions([
                CreateAction::make()
                    ->authorize(function () {
                        $ownerRecord = $this->getOwnerRecord();
                        return auth()->user()->can('create', [Alert::class, $ownerRecord instanceof Prospect ? $ownerRecord : null]);
                    })
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['created_by'] = auth()->id();

                        return $data;
                    }),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
