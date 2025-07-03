<?php

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
