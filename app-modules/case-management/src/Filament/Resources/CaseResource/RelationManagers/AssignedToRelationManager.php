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

namespace AdvisingApp\CaseManagement\Filament\Resources\CaseResource\RelationManagers;

use App\Models\User;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\Scopes\HasLicense;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use App\Filament\Resources\UserResource;
use Filament\Forms\Components\TextInput;
use App\Filament\Tables\Columns\IdColumn;
use Illuminate\Database\Query\Expression;
use AdvisingApp\CaseManagement\Models\ServiceRequest;
use Filament\Resources\RelationManagers\RelationManager;
use AdvisingApp\CaseManagement\Enums\CaseAssignmentStatus;
use AdvisingApp\CaseManagement\Models\ServiceRequestAssignment;

class AssignedToRelationManager extends RelationManager
{
    protected static string $relationship = 'assignedTo';

    protected static ?string $recordTitleAttribute = 'name';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('user.full')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                IdColumn::make(),
                TextColumn::make('user.name')
                    ->label('Name'),
            ])
            ->paginated(false)
            ->headerActions([
                Action::make('reassign-service-request')
                    ->label('Reassign Case')
                    ->color('gray')
                    ->action(fn (array $data) => $this->getOwnerRecord()->assignments()->create([
                        'user_id' => $data['userId'],
                        'assigned_by_id' => auth()->user()?->id ?? null,
                        'assigned_at' => now(),
                        'status' => CaseAssignmentStatus::Active,
                    ]))
                    ->form([
                        Select::make('userId')
                            ->label('Reassign Case To')
                            ->searchable()
                            ->getSearchResultsUsing(fn (string $search): array => User::query()
                                ->tap(new HasLicense($this->getOwnerRecord()->respondent->getLicenseType()))
                                ->where(new Expression('lower(name)'), 'like', '%' . str($search)->lower() . '%')
                                ->pluck('name', 'id')
                                ->all())
                            ->getOptionLabelUsing(fn ($value): ?string => User::find($value)?->name)
                            ->placeholder('Search for and select a User')
                            ->required(),
                    ]),
            ])
            ->actions([
                ViewAction::make()
                    ->url(fn (ServiceRequestAssignment $assignment) => UserResource::getUrl('view', ['record' => $assignment->user])),
            ]);
    }

    public function getOwnerRecord(): ServiceRequest
    {
        /** @var ServiceRequest $record */
        $record = parent::getOwnerRecord();

        return $record;
    }
}
