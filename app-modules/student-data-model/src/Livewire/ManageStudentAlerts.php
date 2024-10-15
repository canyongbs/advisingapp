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

namespace AdvisingApp\StudentDataModel\Livewire;

use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Infolists\Infolist;
use Filament\Forms\Components\Select;
use Illuminate\Support\Facades\Cache;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use AdvisingApp\Alert\Enums\AlertStatus;
use App\Filament\Tables\Columns\IdColumn;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Filters\SelectFilter;
use AdvisingApp\Alert\Enums\AlertSeverity;
use Filament\Infolists\Components\TextEntry;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use AdvisingApp\StudentDataModel\Models\Student;
use Filament\Resources\RelationManagers\RelationManager;
use AdvisingApp\StudentDataModel\Filament\Resources\StudentResource;

class ManageStudentAlerts extends RelationManager
{
    protected static string $resource = StudentResource::class;

    protected static string $relationship = 'alerts';

    // protected static ?string $title = 'Alerts';

    // protected static ?string $label = 'Alerts';

    // protected static string $view = 'student-data-model::livewire.manage-student-alerts';

    // public static function getNavigationItems(array $urlParameters = []): array
    // {
    //     $item = parent::getNavigationItems($urlParameters)[0];

    //     $ownerRecord = $urlParameters['record'];

    //     /** @var Student $ownerRecord */
    //     $alertsCount = Cache::tags('alert-count')
    //         ->remember(
    //             "alert-count-{$ownerRecord->getKey()}",
    //             now()->addMinutes(5),
    //             function () use ($ownerRecord): int {
    //                 return $ownerRecord->alerts()->status(AlertStatus::Active)->count();
    //             },
    //         );

    //     $item->badge($alertsCount > 0 ? $alertsCount : null, color: 'danger');

    //     return [$item];
    // }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                TextEntry::make('description'),
                TextEntry::make('severity'),
                TextEntry::make('suggested_intervention'),
                TextEntry::make('status'),
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
                Select::make('status')
                    ->options(AlertStatus::class)
                    ->selectablePlaceholder(false)
                    ->default(AlertStatus::default())
                    ->required()
                    ->enum(AlertStatus::class),
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
                TextColumn::make('status')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('severity')
                    ->options(AlertSeverity::class),
                SelectFilter::make('status')
                    ->options(AlertStatus::class),
            ])
            ->headerActions([
                CreateAction::make()
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
