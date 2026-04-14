<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS Inc. All rights reserved.

    Advising App® is licensed under the Elastic License 2.0. For more details,
    see https://github.com/canyongbs/advisingapp/blob/main/LICENSE.

    Notice:

    - You may not provide the software to third parties as a hosted or managed
      service, where the service provides users with access to any substantial set of
      the features or functionality of the software.
    - You may not move, change, disable, or circumvent the license key functionality
      in the software, and you may not remove or obscure any functionality in the
      software that is protected by the license key.
    - You may not alter, remove, or obscure any licensing, copyright, or other notices
      of the licensor in the software. Any use of the licensor’s trademarks is subject
      to applicable law.
    - Canyon GBS Inc. respects the intellectual property rights of others and expects the
      same in return. Canyon GBS® and Advising App® are registered trademarks of
      Canyon GBS Inc., and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS Inc.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace AdvisingApp\Prospect\Filament\Resources\Prospects\Tables\Operators;

use AdvisingApp\Application\Models\ApplicationSubmissionState;
use AdvisingApp\Prospect\Models\Prospect;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Component;
use Filament\Tables\Filters\QueryBuilder\Constraints\Operators\Operator;
use Illuminate\Database\Eloquent\Builder;

class IsInApplicationSubmissionStateOperator extends Operator
{
    public function getName(): string
    {
        return 'isInApplicationSubmissionState';
    }

    public function getLabel(): string
    {
        return $this->isInverse()
            ? 'Is not in state'
            : 'Is in state';
    }

    public function getSummary(): string
    {
        $stateIds = $this->getSettings()['states'] ?? [];

        $stateNames = ApplicationSubmissionState::whereKey($stateIds)
            ->pluck('name')
            ->join(', ', ' and ');

        return $this->isInverse()
            ? "Admissions state is not {$stateNames}"
            : "Admissions state is {$stateNames}";
    }

    /**
     * @return array<Component>
     */
    public function getFormSchema(): array
    {
        return [
            Select::make('states')
                ->label('State')
                ->options(function (?array $state) {
                    $selectedIds = $state ?? [];

                    return ApplicationSubmissionState::query()
                        ->where(function (Builder $query) use ($selectedIds) {
                            $query->withoutArchived();  // @phpstan-ignore method.notFound

                            if (! empty($selectedIds)) {
                                $query->orWhereIn('id', $selectedIds);
                            }
                        })
                        ->pluck('name', 'id');
                })
                ->multiple()
                ->preload()
                ->required()
                ->columnSpanFull(),
        ];
    }

    /**
     * @param  Builder<Prospect>  $query
     *
     * @return Builder<Prospect>
     */
    public function apply(Builder $query, string $qualifiedColumn): Builder
    {
        $stateIds = $this->getSettings()['states'] ?? [];

        return $query->{$this->isInverse() ? 'whereDoesntHave' : 'whereHas'}(
            'applicationSubmissions',
            fn (Builder $query) => $query->whereIn('state_id', $stateIds),
        );
    }
}
