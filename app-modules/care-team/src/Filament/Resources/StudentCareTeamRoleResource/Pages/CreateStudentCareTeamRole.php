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

namespace AdvisingApp\CareTeam\Filament\Resources\StudentCareTeamRoleResource\Pages;

use AdvisingApp\CareTeam\Filament\Resources\StudentCareTeamRoleResource;
use AdvisingApp\CareTeam\Models\CareTeamRole;
use App\Enums\CareTeamRoleType;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Pages\CreateRecord;

class CreateStudentCareTeamRole extends CreateRecord
{
    protected static string $resource = StudentCareTeamRoleResource::class;

    protected ?bool $hasDatabaseTransactions = true;

    public string $name;

    public CareTeamRoleType $type;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->string(),
                Toggle::make('is_default')
                    ->label('Default')
                    ->hint(function (?CareTeamRole $record, $state): ?string {
                        if ($record?->is_default) {
                            return null;
                        }

                        if (! $state) {
                            return null;
                        }

                        $currentDefault = CareTeamRole::query()
                            ->where('is_default', true)
                            ->where('type', CareTeamRoleType::Student)
                            ->value('name');

                        if (blank($currentDefault)) {
                            return null;
                        }

                        return "The current default care team role is '{$currentDefault}', you are replacing it.";
                    })
                    ->hintColor('danger')
                    ->columnStart(1)
                    ->live(),
            ]);
    }

    protected function beforeCreate(): void
    {
        /** @var CareTeamRole $record */
        $record = $this->form->getState();

        if ($record['is_default']) {
            CareTeamRole::query()
                ->where('is_default', true)
                ->update(['is_default' => false]);
        }
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['type'] = CareTeamRoleType::Student;

        return $data;
    }
}
