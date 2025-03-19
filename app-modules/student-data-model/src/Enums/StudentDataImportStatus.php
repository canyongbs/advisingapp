<?php

/*
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

    Notice:

    - This software is closed source and the source code is a trade secret.
    - You may not alter, remove, or obscure any licensing, copyright, or other notices
      of the licensor in the software.
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS™ is a registered trademarks of Canyon GBS LLC, and we are
      committed to enforcing and protecting our trademarks vigorously.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace AdvisingApp\StudentDataModel\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum StudentDataImportStatus implements HasColor, HasIcon, HasLabel
{
    case Pending;

    case Processing;

    case Completed;

    case Canceled;

    public function getColor(): string
    {
        return match ($this) {
            self::Pending => 'gray',
            self::Processing => 'info',
            self::Completed => 'success',
            self::Canceled => 'danger',
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::Pending => 'heroicon-m-clock',
            self::Processing => 'heroicon-m-arrow-path',
            self::Completed => 'heroicon-m-check',
            self::Canceled => 'heroicon-m-x-mark',
        };
    }

    public function getLabel(): string
    {
        return $this->name;
    }
}
