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

namespace AdvisingApp\Prospect\Filament\Resources\ProspectResource\Pages;

use AdvisingApp\CareTeam\Models\CareTeam;
use AdvisingApp\CareTeam\Models\CareTeamRole;
use AdvisingApp\Prospect\Concerns\ProspectHolisticViewPage;
use AdvisingApp\Prospect\Filament\Resources\ProspectResource;
use AdvisingApp\Prospect\Filament\Resources\ProspectResource\Pages\Concerns\HasProspectHeader;
use AdvisingApp\Prospect\Models\Prospect;
use App\Enums\CareTeamRoleType;
use App\Filament\Resources\UserResource;
use App\Filament\Tables\Columns\IdColumn;
use App\Models\Scopes\HasLicense;
use App\Models\User;
use Filament\Forms\ComponentContainer;
use Filament\Forms\Components\Select;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Tables\Actions\AttachAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DetachAction;
use Filament\Tables\Actions\DetachBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ManageProspectCareTeam extends ManageRelatedRecords
{
    use ProspectHolisticViewPage;
    use HasProspectHeader;

    protected static string $resource = ProspectResource::class;

    protected static string $relationship = 'careTeam';

    // TODO: Automatically set from Filament based on relationship name
    protected static ?string $navigationLabel = 'Care Team';

    // TODO: Automatically set from Filament based on relationship name
    protected static ?string $breadcrumb = 'Care Team';

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    //TODO: manually override check canAccess for policy

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
                TextColumn::make('careTeams.prospectCareTeamRole.name')
                    ->label('Role')
                    ->badge()
                    ->visible(CareTeamRole::where('type', CareTeamRoleType::Prospect)->count() > 0),
            ])
            ->headerActions([
                AttachAction::make()
                    ->label('New')
                    ->modalHeading(function () {
                        /** @var Prospect $prospect */
                        $prospect = $this->getOwnerRecord();

                        return "Add a User to {$prospect->display_name}'s Care Team";
                    })
                    ->modalSubmitActionLabel('Add')
                    ->attachAnother(false)
                    ->color('primary')
                    ->mountUsing(fn (ComponentContainer $form) => $form->fill([
                        'care_team_role_id' => CareTeamRoleType::prospectDefault()?->getKey(),
                    ]))
                    ->form([
                        Select::make('recordId')
                            ->label('User')
                            ->searchable()
                            ->required()
                            ->options(User::query()->tap(new HasLicense(Prospect::getLicenseType()))->whereDoesntHave('prospectCareTeam')->pluck('name', 'id')),
                        Select::make('care_team_role_id')
                            ->label('Role')
                            ->relationship('careTeamRole', 'name', fn (Builder $query) => $query->where('type', CareTeamRoleType::Prospect))
                            ->searchable()
                            ->model(CareTeam::class)
                            ->visible(CareTeamRole::where('type', CareTeamRoleType::Prospect)->count() > 0),
                    ])
                    ->successNotificationTitle(function (array $data) {
                        /** @var Prospect $prospect */
                        $prospect = $this->getOwnerRecord();

                        $record = User::find($data['recordId']);

                        return "{$record->name} was added to {$prospect->display_name}'s Care Team";
                    }),
            ])
            ->actions([
                DetachAction::make()
                    ->label('Remove')
                    ->modalHeading(function (User $record) {
                        /** @var Prospect $prospect */
                        $prospect = $this->getOwnerRecord();

                        return "Remove {$record->name} from {$prospect->display_name}'s Care Team";
                    })
                    ->modalSubmitActionLabel('Remove')
                    ->successNotificationTitle(function (User $record) {
                        /** @var Prospect $prospect */
                        $prospect = $this->getOwnerRecord();

                        return "{$record->name} was removed from {$prospect->display_name}'s Care Team";
                    }),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DetachBulkAction::make()
                        ->label('Remove selected')
                        ->modalHeading(function () {
                            /** @var Prospect $prospect */
                            $prospect = $this->getOwnerRecord();

                            return "Remove selected users from {$prospect->display_name}'s Care Team";
                        })
                        ->modalSubmitActionLabel('Remove')
                        ->successNotificationTitle(function () {
                            /** @var Prospect $prospect */
                            $prospect = $this->getOwnerRecord();

                            return "All selected users were removed from {$prospect->display_name}'s Care Team";
                        }),
                ]),
            ])
            ->emptyStateHeading('No Users')
            ->inverseRelationship('prospectCareTeams');
    }
}
