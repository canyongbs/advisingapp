<?php

namespace AdvisingApp\StudentDataModel\Filament\Resources\EducatableResource\Pages\Concerns;

use App\Models\User;
use Filament\Tables\Table;
use App\Models\Scopes\HasLicense;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use App\Filament\Resources\UserResource;
use App\Filament\Tables\Columns\IdColumn;
use Filament\Tables\Actions\AttachAction;
use Filament\Tables\Actions\DetachAction;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DetachBulkAction;
use AdvisingApp\StudentDataModel\Models\Student;

trait CanManageEducatableSubscriptions
{
    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                IdColumn::make(),
                TextColumn::make('name')
                    ->url(fn ($record) => UserResource::getUrl('view', ['record' => $record]))
                    ->color('primary'),
                TextColumn::make('pivot.created_at')
                    ->label('Subscribed At'),
            ])
            ->headerActions([
                AttachAction::make()
                    ->label('New')
                    ->modalHeading(function () {
                        /** @var Student $student */
                        $student = $this->getOwnerRecord();

                        return 'Subscribe a User to ' . $student->display_name;
                    })
                    ->modalSubmitActionLabel('Subscribe')
                    ->attachAnother(false)
                    ->color('primary')
                    ->recordSelect(
                        fn (Select $select) => $select->placeholder('Select a User'),
                    )
                    ->recordSelectOptionsQuery(
                        fn (Builder $query) => $query->tap(new HasLicense(Student::getLicenseType())),
                    )
                    ->successNotificationTitle(function (User $record) {
                        /** @var Student $student */
                        $student = $this->getOwnerRecord();

                        return "{$record->name} was subscribed to {$student->display_name}";
                    }),
            ])
            ->actions([
                DetachAction::make()
                    ->label('Unsubscribe')
                    ->modalHeading(function (User $record) {
                        /** @var Student $student */
                        $student = $this->getOwnerRecord();

                        return "Unsubscribe {$record->name} from {$student->display_name}";
                    })
                    ->modalSubmitActionLabel('Unsubscribe')
                    ->successNotificationTitle(function (User $record) {
                        /** @var Student $student */
                        $student = $this->getOwnerRecord();

                        return "{$record->name} was unsubscribed from {$student->display_name}";
                    }),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DetachBulkAction::make()
                        ->label('Unsubscribe selected')
                        ->modalHeading(function () {
                            /** @var Student $student */
                            $student = $this->getOwnerRecord();

                            return "Unsubscribe selected from {$student->display_name}";
                        })
                        ->modalSubmitActionLabel('Unsubscribe')
                        ->successNotificationTitle(function () {
                            /** @var Student $student */
                            $student = $this->getOwnerRecord();

                            return "All selected were unsubscribed from {$student->display_name}";
                        }),
                ]),
            ])
            ->emptyStateHeading('No Subscriptions')
            ->inverseRelationship('studentSubscriptions');
    }
}
