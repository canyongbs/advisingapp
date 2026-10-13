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

namespace AdvisingApp\Prospect\Filament\Resources\Prospects\Pages;

use AdvisingApp\Notification\Models\Subscription;
use AdvisingApp\Prospect\Concerns\ProspectHolisticViewPage;
use AdvisingApp\Prospect\Filament\Resources\Prospects\ProspectResource;
use AdvisingApp\Prospect\Models\Prospect;
use App\Filament\Resources\Users\UserResource;
use App\Filament\Tables\Columns\IdColumn;
use App\Models\Scopes\HasLicense;
use App\Models\Scopes\WithoutAnyAdmin;
use App\Models\User;
use BackedEnum;
use Filament\Actions\AttachAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DetachAction;
use Filament\Actions\DetachBulkAction;
use Filament\Forms\Components\Select;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ManageProspectSubscriptions extends ManageRelatedRecords
{
    use ProspectHolisticViewPage;

    protected static string $resource = ProspectResource::class;

    protected static string $relationship = 'subscribedUsers';

    // TODO: Automatically set from Filament based on relationship name
    protected static ?string $navigationLabel = 'Followers';

    // TODO: Automatically set from Filament based on relationship name
    protected static ?string $breadcrumb = 'Followers';

    protected static ?string $title = 'Followers';

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-user';

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
                TextColumn::make('pivot.created_at')
                    ->dateTime()
                    ->label('Followed At'),
            ])
            ->headerActions([
                AttachAction::make()
                    ->authorize(fn (): bool => auth()->user()->can('create', Subscription::class))
                    ->label('New')
                    ->modalHeading(function () {
                        /** @var Prospect $prospect */
                        $prospect = $this->getOwnerRecord();

                        return 'Add a Follower to ' . $prospect->display_name;
                    })
                    ->modalSubmitActionLabel('Add Follower')
                    ->attachAnother(false)
                    ->color('primary')
                    ->recordSelect(
                        fn (Select $select) => $select->placeholder('Select a User'),
                    )
                    ->recordSelectOptionsQuery(
                        fn (Builder $query) => $query
                            ->tap(new HasLicense(Prospect::getLicenseType()))
                            ->tap(new WithoutAnyAdmin()),
                    )
                    ->successNotificationTitle(function (User $record) {
                        /** @var Prospect $prospect */
                        $prospect = $this->getOwnerRecord();

                        return "{$record->name} is now following {$prospect->display_name}";
                    }),
            ])
            ->recordActions([
                DetachAction::make()
                    ->authorize(fn (): bool => auth()->user()->can('deleteAny', Subscription::class))
                    ->label('Unfollow')
                    ->modalHeading(function (User $record) {
                        /** @var Prospect $prospect */
                        $prospect = $this->getOwnerRecord();

                        return "Remove {$record->name} as a follower of {$prospect->display_name}";
                    })
                    ->modalSubmitActionLabel('Unfollow')
                    ->successNotificationTitle(function (User $record) {
                        /** @var Prospect $prospect */
                        $prospect = $this->getOwnerRecord();

                        return "{$record->name} is no longer following {$prospect->display_name}";
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DetachBulkAction::make()
                        ->authorize(fn (): bool => auth()->user()->can('deleteAny', Subscription::class))
                        ->label('Unfollow selected')
                        ->modalHeading(function () {
                            /** @var Prospect $prospect */
                            $prospect = $this->getOwnerRecord();

                            return "Remove selected followers from {$prospect->display_name}";
                        })
                        ->modalSubmitActionLabel('Unfollow')
                        ->successNotificationTitle(function () {
                            /** @var Prospect $prospect */
                            $prospect = $this->getOwnerRecord();

                            return "All selected are no longer following {$prospect->display_name}";
                        }),
                ]),
            ])
            ->emptyStateHeading('No Followers')
            ->inverseRelationship('prospectSubscriptions');
    }
}
