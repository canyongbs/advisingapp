<?php

namespace Assist\AssistDataModel\Filament\Resources\StudentResource\Pages;

use Assist\AssistDataModel\Filament\Resources\StudentResource;
use Assist\Notifications\Filament\Resources\SubscriptionResource\Pages\ManageSubscriptions;

class ManageStudentSubscriptions extends ManageSubscriptions
{
    protected static string $resource = StudentResource::class;
}
