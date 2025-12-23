<?php

namespace Advisingapp\Interaction\Enums;

use Exception;
use Filament\Support\Contracts\HasLabel;

enum InteractableType: string implements HasLabel
{
    case Prospect = 'prospect';

    case Student = 'student';

    public function getLabel(): string
    {
        return $this->name;
    }

    public static function fromMorphClass(string $morphClass): self
    {
        return match ($morphClass) {
            'prospect' => InteractableType::Prospect,
            'student' => InteractableType::Student,
            default => throw new Exception('Invalid interactable type'),
        };
    }
}
