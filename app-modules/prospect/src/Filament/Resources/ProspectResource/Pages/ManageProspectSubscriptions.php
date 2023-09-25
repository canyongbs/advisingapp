<?php

namespace Assist\Prospect\Filament\Resources\ProspectResource\Pages;

use Assist\Prospect\Filament\Resources\ProspectResource;
use Assist\Notifications\Filament\Resources\SubscriptionResource\Pages\ManageSubscriptions;

class ManageProspectSubscriptions extends ManageSubscriptions
{
    protected static string $resource = ProspectResource::class;
}
