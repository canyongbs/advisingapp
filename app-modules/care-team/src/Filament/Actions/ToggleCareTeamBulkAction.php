<?php

namespace Assist\CareTeam\Filament\Actions;

use App\Models\User;
use Filament\Tables\Actions\BulkAction;
use Illuminate\Database\Eloquent\Collection;
use Assist\AssistDataModel\Models\Contracts\Educatable;

class ToggleCareTeamBulkAction extends BulkAction
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->icon('heroicon-s-user-group');

        $this->action(function (Collection $records) {
            return $records
                ->each(function (Educatable $record) {
                    /** @var User $user */
                    $user = auth()->user();

                    if ($record->careTeam()->where('user_id', $user->id)->exists()) {
                        $record->careTeam()->detach($user);
                    } else {
                        $record->careTeam()->attach($user);
                    }
                });
        });

        $this->deselectRecordsAfterCompletion();
    }

    public static function getDefaultName(): ?string
    {
        return 'toggleCareTeam';
    }
}
