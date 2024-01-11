<?php

namespace AdvisingApp\Authorization\Filament\Widgets;

use Filament\Widgets\Widget;
use App\Models\Authenticatable;
use AdvisingApp\Authorization\Enums\LicenseType;

class UnlicensedNotice extends Widget
{
    protected static string $view = 'authorization::filament.widgets.unlicensed-notice';

    protected static bool $isLazy = false;

    protected int | string | array $columnSpan = 'full';

    public static function canView(): bool
    {
        /** @var Authenticatable $user */
        $user = auth()->user();

        return ! $user->hasAnyLicense(LicenseType::cases());
    }
}
