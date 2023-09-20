<?php

namespace Assist\CaseloadManagement\Enums;

use Illuminate\Support\Collection;
use Assist\Prospect\Models\Prospect;
use Assist\AssistDataModel\Models\Student;
use Illuminate\Database\Eloquent\Relations\Relation;

enum CaseloadSubject: string
{
    case Student = 'student';
    case Prospect = 'prospect';

    public static function display(): Collection
    {
        return collect(CaseloadSubject::cases())
            ->mapWithKeys(fn (CaseloadSubject $item) => [
                Relation::getMorphedModel($item->value) => $item->label(),
            ]);
    }

    public function class(): string
    {
        return match ($this) {
            CaseloadSubject::Student => Student::class,
            CaseloadSubject::Prospect => Prospect::class,
        };
    }

    public function label(): string
    {
        return match ($this) {
            CaseloadSubject::Student => 'Student',
            CaseloadSubject::Prospect => 'Prospect',
        };
    }

    public function query()
    {
        return match ($this) {
            CaseloadSubject::Student => Student::query(),
            CaseloadSubject::Prospect => Prospect::query(),
        };
    }
}
