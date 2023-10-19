<?php

namespace Assist\CaseloadManagement\Enums;

use App\Imports\Importer;
use Assist\Prospect\Models\Prospect;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Database\Eloquent\Builder;
use Assist\AssistDataModel\Models\Student;
use Assist\CaseloadManagement\Importers\StudentCaseloadSubjectImporter;
use Assist\CaseloadManagement\Importers\ProspectCaseloadSubjectImporter;

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

    public function class(): string
    {
        return match ($this) {
            CaseloadModel::Student => Student::class,
            CaseloadModel::Prospect => Prospect::class,
        };
    }

    public static function tryFromCaseOrValue(CaseloadModel | string $value): ?CaseloadModel
    {
        if ($value instanceof CaseloadModel) {
            return $value;
        }

        return static::tryFrom($value);
    }

    /**
     * @return class-string<Importer>
     */
    public function getSubjectImporter(): string
    {
        return match ($this) {
            static::Prospect => ProspectCaseloadSubjectImporter::class,
            static::Student => StudentCaseloadSubjectImporter::class,
        };
    }
}
