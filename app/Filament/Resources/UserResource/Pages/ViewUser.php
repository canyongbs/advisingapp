<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Models\User;
use Filament\Actions\EditAction;
use App\Filament\Resources\UserResource;
use Filament\Resources\Pages\ViewRecord;
use STS\FilamentImpersonate\Pages\Actions\Impersonate;

class ViewUser extends ViewRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        /** @var User $user */
        $user = $this->getRecord();

        return [
            Impersonate::make()
                ->record($user),
            EditAction::make(),
        ];
    }
}
