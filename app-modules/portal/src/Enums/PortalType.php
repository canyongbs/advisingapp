<?php

namespace AdvisingApp\Portal\Enums;

use Filament\Support\Contracts\HasLabel;

enum PortalType: string implements HasLabel
{
    case KnowledgeManagement = 'knowledge_management';

    public function getLabel(): ?string
    {
        return match ($this) {
            PortalType::KnowledgeManagement => 'Knowledge Management',
        };
    }
}
