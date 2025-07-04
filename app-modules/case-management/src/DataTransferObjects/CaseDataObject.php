<?php

namespace AdvisingApp\CaseManagement\DataTransferObjects;

use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Optional;

#[MapName(SnakeCaseMapper::class)]
class CaseDataObject extends Data
{
    public function __construct(
        public string|Optional $divisionId,
        public string|Optional $statusId,
        public string $typeId,
        public string|Optional $priorityId,
        public string|Optional $closeDetails,
        public string|Optional $resDetails,
        public string $respondentType,
        public string $respondentId,
    ) {}

    /**
     * @param array<string, string|Optional|null> $data
     *
     * @return self
     */
    public static function fromData(array $data): self
    {
        return new self(
            divisionId: $data['division_id'] ?? Optional::create(),
            statusId: $data['status_id'] ?? Optional::create(),
            typeId: $data['type_id'],
            priorityId: $data['priority_id'] ?? Optional::create(),
            closeDetails: $data['close_details'] ?? Optional::create(),
            resDetails: $data['res_details'] ?? Optional::create(),
            respondentType: $data['respondent_type'],
            respondentId: $data['respondent_id'],
        );
    }
}
