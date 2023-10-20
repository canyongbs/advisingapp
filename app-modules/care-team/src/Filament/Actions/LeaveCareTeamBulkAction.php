<?php

namespace Assist\CareTeam\Filament\Actions;

use App\Models\User;
use Filament\Tables\Actions\BulkAction;
use Illuminate\Database\Eloquent\Collection;
use Assist\AssistDataModel\Models\Contracts\Educatable;

class LeaveCareTeamBulkAction extends BulkAction
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

                    $record->careTeam()->detach($user);
                });
        });

        $this->deselectRecordsAfterCompletion();
    }

    public static function getDefaultName(): ?string
    {
        return 'leaveCareTeam';
    }
}
