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

trait CanManageEducatableCareTeam
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
                TextColumn::make('job_title'),
            ])
            ->headerActions([
                AttachAction::make()
                    ->label('New')
                    ->modalHeading(function () {
                        /** @var Student $student */
                        $student = $this->getOwnerRecord();

                        return "Add a User to {$student->display_name}'s Care Team";
                    })
                    ->modalSubmitActionLabel('Add')
                    ->attachAnother(false)
                    ->color('primary')
                    ->recordSelect(
                        fn (Select $select) => $select->placeholder('Select Users'),
                    )
                    ->multiple()
                    ->recordSelectOptionsQuery(
                        fn (Builder $query) => $query->tap(new HasLicense(Student::getLicenseType())),
                    )
                    ->successNotificationTitle(function (array $data) {
                        /** @var Student $student */
                        $student = $this->getOwnerRecord();

                        if (count($data['recordId']) > 1) {
                            return count($data['recordId']) . " users were added to {$student->display_name}'s Care Team";
                        }
                        $record = User::find($data['recordId'][0]);

                        return "{$record->name} was added to {$student->display_name}'s Care Team";
                    }),
            ])
            ->actions([
                DetachAction::make()
                    ->label('Remove')
                    ->modalHeading(function (User $record) {
                        /** @var Student $student */
                        $student = $this->getOwnerRecord();

                        return "Remove {$record->name} from {$student->display_name}'s Care Team";
                    })
                    ->modalSubmitActionLabel('Remove')
                    ->successNotificationTitle(function (User $record) {
                        /** @var Student $student */
                        $student = $this->getOwnerRecord();

                        return "{$record->name} was removed from {$student->display_name}'s Care Team";
                    }),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DetachBulkAction::make()
                        ->label('Remove selected')
                        ->modalHeading(function () {
                            /** @var Student $student */
                            $student = $this->getOwnerRecord();

                            return "Remove selected users from {$student->display_name}'s Care Team";
                        })
                        ->modalSubmitActionLabel('Remove')
                        ->successNotificationTitle(function () {
                            /** @var Student $student */
                            $student = $this->getOwnerRecord();

                            return "All selected users were removed from {$student->display_name}'s Care Team";
                        }),
                ]),
            ])
            ->emptyStateHeading('No Users')
            ->inverseRelationship('studentCareTeams');
    }
}
