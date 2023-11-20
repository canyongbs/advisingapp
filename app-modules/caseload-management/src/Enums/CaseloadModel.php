<?php

/*
<COPYRIGHT>

Copyright © 2022-2023, Canyon GBS LLC

All rights reserved.

This file is part of a project developed using Laravel, which is an open-source framework for PHP.
Canyon GBS LLC acknowledges and respects the copyright of Laravel and other open-source
projects used in the development of this solution.

This project is licensed under the Affero General Public License (AGPL) 3.0.
For more details, see https://github.com/canyongbs/assistbycanyongbs/blob/main/LICENSE.

Notice:
- The copyright notice in this file and across all files and applications in this
 repository cannot be removed or altered without violating the terms of the AGPL 3.0 License.
- The software solution, including services, infrastructure, and code, is offered as a
 Software as a Service (SaaS) by Canyon GBS LLC.
- Use of this software implies agreement to the license terms and conditions as stated
 in the AGPL 3.0 License.

For more information or inquiries please visit our website at
https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

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
