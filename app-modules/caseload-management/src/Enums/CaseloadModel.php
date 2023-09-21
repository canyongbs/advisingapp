<?php

namespace Assist\CaseloadManagement\Enums;

use Assist\Prospect\Models\Prospect;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Database\Eloquent\Builder;
use Assist\AssistDataModel\Models\Student;

enum CaseloadModel: string implements HasLabel
{
    case Prospect = 'prospect';

    case Student = 'student';

    public function getLabel(): ?string
    {
        return $this->name;
    }

    public static function default(): CaseloadModel
    {
        return CaseloadModel::Student;
    }

    public function query(): Builder
    {
        return match ($this) {
            CaseloadModel::Student => Student::query(),
            CaseloadModel::Prospect => Prospect::query(),
        };
    }
}
