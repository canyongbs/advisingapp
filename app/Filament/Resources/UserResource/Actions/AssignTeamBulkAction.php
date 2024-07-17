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

namespace App\Filament\Resources\UserResource\Actions;

use Exception;
use App\Models\User;
use AdvisingApp\Team\Models\Team;
use Filament\Support\Enums\MaxWidth;
use Filament\Forms\Components\Select;
use Filament\Tables\Actions\BulkAction;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Collection;

class AssignTeamBulkAction extends BulkAction
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->icon('heroicon-o-user-group')
            ->modalWidth(MaxWidth::Small)
            ->modalDescription(
                fn (Collection $records): string => 'This bulk action will overwrite any prior team assignments for the selected ' . ((count($records) > 1) ? 'users' : 'user') . '.'
            )
            ->fillForm(fn (Collection $records): array => [
                'records' => $records,
            ])
            ->form([
                Select::make('team')
                    ->label('Team')
                    ->options(Team::pluck('name', 'id'))
                    ->searchable()
                    ->required(),
            ])
            ->action(function (array $data, Collection $records) {
                $success = 0;
                $fail = 0;
                $records->each(function (User $record) use ($data, &$success, &$fail) {
                    try {
                        $record->assignTeam($data['team']);
                        $success++;
                    } catch (Exception $e) {
                        report($e);
                        $fail++;
                    }
                });

                if ($fail > 0) {
                    Notification::make()
                        ->title('Assigned Team')
                        ->body($fail . ' ' . ((count($records) > 1) ? 'users were' : 'user was') . ' fail to added to the team.')
                        ->success()
                        ->send();
                } else {
                    Notification::make()
                        ->title('Assigned Team')
                        ->body($success . ' ' . ((count($records) > 1) ? 'users were' : 'user was') . ' successfully added to the team.')
                        ->success()
                        ->send();
                }
            });
    }

    public static function getDefaultName(): ?string
    {
        return 'Assign team';
    }
}
