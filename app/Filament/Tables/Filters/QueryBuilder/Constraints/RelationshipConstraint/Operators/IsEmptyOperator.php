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

namespace App\Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint\Operators;

use Illuminate\Database\Eloquent\Builder;
use App\Filament\Tables\Filters\QueryBuilder\Constraints\Operators\Operator;

class IsEmptyOperator extends Operator
{
    public function getName(): string
    {
        return 'isEmpty';
    }

    public function getLabel(): string
    {
        return __(
            $this->isInverse() ?
                'filament-tables::filters/query-builder.operators.relationship.is_empty.label.inverse' :
                'filament-tables::filters/query-builder.operators.relationship.is_empty.label.direct',
        );
    }

    public function getSummary(): string
    {
        return __(
            $this->isInverse() ?
                'filament-tables::filters/query-builder.operators.relationship.is_empty.summary.inverse' :
                'filament-tables::filters/query-builder.operators.relationship.is_empty.summary.direct',
            ['relationship' => $this->getConstraint()->getAttributeLabel()],
        );
    }

    public function applyToBaseQuery(Builder $query): Builder
    {
        return $query->{$this->isInverse() ? 'has' : 'doesntHave'}($this->getConstraint()->getRelationshipName());
    }
}
