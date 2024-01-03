<?php

namespace AdvisingApp\Analytics\Enums;

use Filament\Support\Contracts\HasLabel;

enum AnalyticsResourceCategoryClassification: string implements HasLabel
{
    case Public = 'public';

    case Internal = 'internal';

    case RestrictedInternal = 'restricted_internal';

    public function getLabel(): ?string
    {
        return str($this->name)->headline();
    }
}
