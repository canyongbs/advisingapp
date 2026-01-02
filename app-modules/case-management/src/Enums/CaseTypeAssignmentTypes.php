<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS LLC. All rights reserved.

    Advising App™ is licensed under the Elastic License 2.0. For more details,
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
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS™ and Advising App™ are registered trademarks of
      Canyon GBS LLC, and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS LLC.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace AdvisingApp\CaseManagement\Enums;

use AdvisingApp\CaseManagement\Services\CaseType\CaseTypeAssigner;
use AdvisingApp\CaseManagement\Services\CaseType\IndividualAssigner;
use AdvisingApp\CaseManagement\Services\CaseType\RoundRobinAssigner;
use AdvisingApp\CaseManagement\Services\CaseType\WorkloadAssigner;
use Filament\Support\Contracts\HasLabel;

// TODO This might belong in a more generalized space so we can re-use this across modules
enum CaseTypeAssignmentTypes: string implements HasLabel
{
    case None = 'none';

    case Individual = 'individual';

    case RoundRobin = 'round-robin';

    case Workload = 'workload';

    public function getLabel(): string
    {
        return str()->headline($this->name);
    }

    public function getAssignerClass(): ?CaseTypeAssigner
    {
        return match ($this) {
            self::Individual => app(IndividualAssigner::class),
            self::RoundRobin => app(RoundRobinAssigner::class),
            self::Workload => app(WorkloadAssigner::class),
            default => null
        };
    }

    public function getDescription(): string
    {
        return match ($this) {
            self::None => 'No assignment is made when this option is selected, allowing cases to remain unassigned until manual intervention. Ideal for flexible workflows where task assignment is determined later.',
            self::Individual => 'All cases are assigned to a specific manager. Best suited for cases with a dedicated resource responsible for managing tasks, ensuring consistent oversight and accountability.',
            self::RoundRobin => 'Cases are distributed evenly among request managers in a circular order. This ensures fair ticket allocation but doesn\'t factor in current workloads.',
            self::Workload => 'Assignments are made based on the current workload, with requests directed to the user handling the fewest open tickets. This method balances workloads, improving efficiency and avoiding bottlenecks.',
        };
    }
}
