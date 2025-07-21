<?php

namespace AdvisingApp\Ai\Enums;

use Filament\Support\Contracts\HasLabel;

enum AiPromptTabs: string implements HasLabel
{
    case Newest = 'newest';

    case MostLoved = 'most';

    case MostViewed = 'most_viewed';

    public function getLabel(): string
    {
        return match ($this) {
            self::Newest => 'Newest',
            self::MostLoved => 'Most 🥰',
            self::MostViewed => 'Most Viewed',
        };
    }
}
